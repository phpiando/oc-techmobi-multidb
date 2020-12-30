<?php namespace Techmobi\Multidb\Console;

use DB;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Techmobi\Multidb\Classes\UpdateManager;

/**
 * Responsible for updating or creating the database.
 * @package techmobi\multidb
 * @author Roni Sommerfeld
 */
class DBCreate extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'multidb:dbcreate';

    /**
     * @var string The console command description.
     */
    protected $description = 'No description provided yet...';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $dbName = $this->option('dbname');
        $dbConnection = $this->option('dbconnection');

        $stringConfig = "database.connections.{$dbConnection}";

        $charset = config("{$stringConfig}.charset", 'utf8mb4');
        $collation = config("{$stringConfig}.collation", 'utf8mb4_unicode_ci');

        $query = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET $charset COLLATE $collation;";

        DB::connection($dbConnection)->statement($query);

        config(["{$stringConfig}.database" => $dbName]);

        UpdateManager::instance()
            ->setNotesOutput($this->output)
            ->update($dbConnection, $dbName);
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
        ];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['--dbname', null, InputOption::VALUE_OPTIONAL],
            ['--dbconnection', null, InputOption::VALUE_OPTIONAL],
        ];
    }
}
