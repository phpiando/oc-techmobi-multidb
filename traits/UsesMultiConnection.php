<?php namespace Techmobi\Multidb\Traits;

use Config;
use Request;
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

        if (!$this->domain) {
            return false;
        }

        $default = env('MULTIDB_CONNECTION', 'multidb');

        $this->connection = $default;
        $this->table = $this->getDatabaseName() . '.' . $this->table;
        // Config::set('database.default', $default);
        Config::set('database.connections.multidb.database', $this->getDatabaseName());
        \Db::connection($this->connection)->reconnect();
    }

    public function getDatabaseName()
    {
        return $this->domain->db_name;
    }

    private function getDomainData()
    {
        //resolve problem with seeder and multidb
        $connectionDefault = config('database.default');
        Config::set('database.default', config('database.original'));

        $currentHostUrl = Request::getHost();

        $this->domain = Domain::whereHas('sites', function ($query) use ($currentHostUrl) {
            $query->where('domain', 'like', '%' . $currentHostUrl . '%');
        })->first();

        Config::set('database.default', $connectionDefault);
    }
}
