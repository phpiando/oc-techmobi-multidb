<?php namespace Techmobi\Multidb\Traits;

use Config;
use DB;
use Request;
use Schema;
use Session;
use Techmobi\Multidb\Models\Domain;

/**
 * Manage connections in models
 * @package techmobi\multidb
 * @author Roni Sommerfeld
 */
trait UsesMultiConnection
{
    private $domain;

    public static function bootUsesMultiConnection()
    {
        static::extend(function ($model) {
            $model->handleDomainConnection();
        });
    }

    private function handleDomainConnection()
    {
        $this->getDomainData();

        if (!$this->getDatabaseName()) {
            return false;
        }

        $connection = env('MULTIDB_CONNECTION', 'multidb');

        $dbTable = $this->getDatabaseName() . '.' . $this->table;

        /*if table not exists in new schema use the table in the database default*/
        if (Schema::hasTable($this->table)) {
            $this->connection = $connection;
            $this->table = $dbTable;

            DB::connection($this->connection)->reconnect();
        }
    }

    public function getDatabaseName()
    {
        return Session::get('techmobi_dbname') ?? ($this->domain->db_name ?? '');
    }

    private function getDomainData()
    {
        //resolve problem with seeder and multidb
        $connectionDefault = config('database.default');
        Config::set('database.default', config('database.original'));

        $currentHostUrl = Request::getHost();

        if (Schema::hasTable((new Domain)->table)) {
            $this->domain = Domain::whereHas('sites', function ($query) use ($currentHostUrl) {
                $query->where('domain', 'like', '%' . $currentHostUrl . '%');
            })->first();
        }

        Config::set('database.default', $connectionDefault);
        Config::set('database.connections.multidb.database', $this->getDatabaseName());
    }
}
