<?php namespace Techmobi\Multidb\Classes;

use Config;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use System\Classes\PluginManager;
use System\Models\PluginVersion;
use Techmobi\Multidb\Classes\VersionManager;
use Techmobi\Multidb\Models\Settings;

/**
 * Update manager
 *
 * Handles the CMS install and update process.
 *
 * @package techmobi\multidb
 * @author Roni Sommerfeld
 * Credits Alexey Bobkov, Samuel Georges
 */
class UpdateManager
{
    use \October\Rain\Support\Traits\Singleton;

    /**
     * @var \System\Classes\PluginManager
     */
    protected $pluginManager;

    /**
     * @var \System\Classes\VersionManager
     */
    protected $versionManager;

    /**
     * @var array The notes for the current operation.
     */
    protected $notes = [];

    /**
     * @var \Illuminate\Console\OutputStyle
     */
    protected $notesOutput;

    /**
     * @var string The name Connection
     */
    protected $connection;

    /**
     * @var string The connection default
     */
    protected $connectionDefault;

    /**
     * @var string The name Database
     */
    protected $database;

    public function init()
    {
        $this->connectionDefault = config('database.default');
        $this->pluginManager = PluginManager::instance();
        $this->versionManager = VersionManager::instance();
    }

    public function update(String $connection, String $database)
    {
        $this->connection = $connection;
        $this->database = $database;
        $this->versionManager->connection = $connection;
        $this->versionManager->databaseName = $database;

        //create or not table migrations
        $this->createRepository();

        /*
         * Update plugins
         */
        $plugins = $this->getPlugins();

        foreach ($plugins as $code => $plugin) {
            $this->updatePlugin($code);
        }
    }

    private function getPlugins()
    {
        $plugins = [];
        $pluginsId = Settings::get('plugins');

        if (is_array($pluginsId) && count($pluginsId) > 0) {
            $plugins = PluginVersion::whereIn('code', $pluginsId)->lists('code', 'code');
        }

        return $plugins;
    }

    /**
     * Runs update on a single plugin
     * @param string $name Plugin name.
     * @return self
     */
    public function updatePlugin($name)
    {
        /*
         * Update the plugin database and version
         */
        if (!($plugin = $this->pluginManager->findByIdentifier($name))) {
            $this->note('<error>Unable to find:</error> ' . $name);
            return;
        }

        $this->note($name);

        $this->versionManager->resetNotes()->setNotesOutput($this->notesOutput);

        if ($this->versionManager->updatePlugin($plugin) !== false) {
            foreach ($this->versionManager->getNotes() as $note) {
                $this->note($note);
            }
        }

        return $this;
    }

    private function createRepository()
    {
        try {
            Schema::connection($this->connection)
                ->create($this->getMigrationTableName(), function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('migration');
                    $table->integer('batch');
                });

            $this->note('Migration table created');

            Schema::create($this->database . '.system_plugin_versions', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('code')->index();
                $table->string('version', 50);
                $table->timestamp('created_at')->nullable();
            });

            $this->note('System Plugins Versions table created');

            Schema::create($this->database . '.system_plugin_history', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('code')->index();
                $table->string('type', 20)->index();
                $table->string('version', 50);
                $table->text('detail')->nullable();
                $table->timestamp('created_at')->nullable();
            });

            $this->note('System Plugins History table created');

            Schema::create($this->database . '.system_files', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('disk_name');
                $table->string('file_name');
                $table->integer('file_size');
                $table->string('content_type');
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('field')->nullable()->index();
                $table->string('attachment_id')->index()->nullable();
                $table->string('attachment_type')->index()->nullable();
                $table->boolean('is_public')->default(true);
                $table->integer('sort_order')->nullable();
                $table->timestamps();
            });

            $this->note('System Files table created');

            Schema::create($this->database . '.deferred_bindings', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('master_type')->index();
                $table->string('master_field')->index();
                $table->string('slave_type')->index();
                $table->string('slave_id')->index();
                $table->string('session_key');
                $table->boolean('is_bind')->default(true);
                $table->timestamps();
            });

            $this->note('Deferred Bindings table created');

            Schema::create($this->database . '.system_parameters', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('namespace', 100);
                $table->string('group', 50);
                $table->string('item', 150);
                $table->text('value')->nullable();
                $table->index(['namespace', 'group', 'item'], 'item_index');
            });
            $this->note('System Parameters table created');

            Schema::create($this->database . '.system_settings', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('item')->nullable()->index();
                $table->mediumtext('value')->nullable();
            });
            $this->note('System Settings table created');
        } catch (Exception $except) {}
    }

    /**
     * Sets an output stream for writing notes.
     * @param Illuminate\Console\Command $output
     * @return self
     */
    public function setNotesOutput($output)
    {
        $this->notesOutput = $output;

        return $this;
    }

    /**
     * Raise a note event for the migrator.
     * @param string $message
     * @return self
     */
    protected function note($message)
    {
        if (null !== $this->notesOutput) {
            $this->notesOutput->writeln($message);
        } else {
            $this->notes[] = $message;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getMigrationTableName()
    {
        return $this->database . '.migrations';
    }
}
