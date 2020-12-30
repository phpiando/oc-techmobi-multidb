<?php namespace Techmobi\Multidb\Classes;

use Artisan;
use Exception;
use Techmobi\Multidb\Models\Domain;
use Techmobi\Multidb\Models\Settings;

/**
 * Responsible for updating or creating the database.
 * @package techmobi\multidb
 * @author Roni Sommerfeld
 */
class SyncDB
{
    use \October\Rain\Support\Traits\Singleton;

    protected $artisan;
    protected $connection;
    protected $domain;

    public function init()
    {
        $this->initConnection();
    }

    private function initConnection()
    {
        $default = env('MULTIDB_CONNECTION', 'multidb');

        if (!$default) {
            throw new Exception("Error, required Connection to MultiDB");
        }

        $this->connection = $default;
    }

    public function startSyncDB(Domain $model)
    {
        $this->domain = $model;

        $this->createDatabase();
    }

    private function createDatabase()
    {
        $dbName = $this->domain->db_name;

        if (empty($this->domain->db_name)) {
            $dbName = trim(Settings::get('prefix_db')) . '';

            if (Settings::get('db_name') == 'hash') {
                $dbName .= uniqid();
            } else {
                $dbName .= str_slug($this->domain->name, "_");
            }
        }

        $options = [
            '--dbname' => $dbName,
            '--dbconnection' => $this->connection,
        ];

        Artisan::call('multidb:dbcreate', $options);

        $this->domain->db_name = $dbName;
        $this->domain->save();
    }
}
