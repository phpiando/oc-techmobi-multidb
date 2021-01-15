<?php namespace Techmobi\Multidb\Classes;

use Artisan;
use Exception;
use Session;
use Techmobi\Multidb\Models\Domain;

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

        $options = [
            '--dbname' => $dbName,
            '--dbconnection' => $this->connection,
        ];

        //temporary dbname
        Session::put('techmobi_dbname', $dbName);

        Artisan::call('multidb:dbcreate', $options);

        Session::forget('techmobi_dbname');
    }
}
