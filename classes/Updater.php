<?php namespace Techmobi\Multidb\Classes;

use Config;
use Db;
use Eloquent;
use Exception;
use File;

/**
 * Database updater
 *
 * Executes database migration and seed scripts based on their filename.
 *
 * @package october\database
 * @author Alexey Bobkov, Samuel Georges
 */
class Updater
{
    public $connection;
    public $database;
    private $defaultConnection;

    /**
     * Sets up a migration or seed file.
     */
    public function setUp($file)
    {
        $object = $this->resolve($file);

        if (null === $object) {
            return false;
        }

        $this->isValidScript($object);

        Eloquent::unguard();

        $this->changeConnection();

        Db::connection($this->connection)
            ->transaction(function () use ($object) {
                if ($object instanceof \October\Rain\Database\Updates\Migration) {
                    $object->up();
                } elseif ($object instanceof \October\Rain\Database\Updates\Seeder) {
                    $object->run();
                }
            });

        Eloquent::reguard();

        $this->rollbackConnection();

        return true;
    }

    private function changeConnection()
    {
        $this->defaultConnection = Config::get('database.default');

        Config::set('database.default', $this->connection);
        Config::set('database.connections.multidb.database', $this->database);
        Db::connection($this->connection)->reconnect();
    }

    private function rollbackConnection()
    {
        Config::set('database.default', $this->defaultConnection);
        Db::connection($this->defaultConnection)->reconnect();
    }

    /**
     * Packs down a migration or seed file.
     */
    public function packDown($file)
    {
        $object = $this->resolve($file);

        if (null === $object) {
            return false;
        }

        $this->isValidScript($object);

        Eloquent::unguard();

        $this->changeConnection();

        Db::transaction(function () use ($object) {
            if ($object instanceof Updates\Migration) {
                $object->down();
            }
        });

        Eloquent::reguard();

        $this->rollbackConnection();

        return true;
    }

    /**
     * Resolve a migration instance from a file.
     * @param  string  $file
     * @return object
     */
    public function resolve($file)
    {
        if (!File::isFile($file)) {
            return;
        }

        require_once $file;

        if ($class = $this->getClassFromFile($file)) {
            return new $class;
        }
    }

    /**
     * Checks if the object is a valid update script.
     */
    protected function isValidScript($object)
    {
        if ($object instanceof \October\Rain\Database\Updates\Migration) {
            return true;
        } elseif ($object instanceof \October\Rain\Database\Updates\Seeder) {
            return true;
        }

        throw new Exception(sprintf(
            'Database script [%s] must inherit October\Rain\Database\Updates\Migration or October\Rain\Database\Updates\Seeder classes',
            get_class($object)
        ));
    }

    /**
     * Extracts the namespace and class name from a file.
     * @param string $file
     * @return string
     */
    public function getClassFromFile($file)
    {
        $fileParser = fopen($file, 'r');
        $class = $namespace = $buffer = '';
        $i = 0;

        while (!$class) {
            if (feof($fileParser)) {
                break;
            }

            $buffer .= fread($fileParser, 512);

            // Prefix and suffix string to prevent unterminated comment warning
            $tokens = token_get_all('/**/' . $buffer . '/**/');

            if (strpos($buffer, '{') === false) {
                continue;
            }

            for (; $i < count($tokens); $i++) {
                /*
                 * Namespace opening
                 */
                if (T_NAMESPACE === $tokens[$i][0]) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if (';' === $tokens[$j]) {
                            break;
                        }

                        $namespace .= is_array($tokens[$j]) ? $tokens[$j][1] : $tokens[$j];
                    }
                }

                /*
                 * Class opening
                 */
                if (T_CLASS === $tokens[$i][0] && '::' !== $tokens[$i - 1][1]) {
                    $class = $tokens[$i + 2][1];
                    break;
                }
            }
        }

        if (!strlen(trim($namespace)) && !strlen(trim($class))) {
            return false;
        }

        return trim($namespace) . '\\' . trim($class);
    }
}
