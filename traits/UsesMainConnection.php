<?php namespace Techmobi\Multidb\Traits;

use Config;

/**
 * Manage connections in models
 * @package techmobi\multidb
 * @author Roni Sommerfeld
 */
trait UsesMainConnection
{
    private $domain;

    public static function bootUsesMainConnection()
    {
        static::extend(function ($model) {
            $model->handleMainConnection();
        });
    }

    private function handleMainConnection()
    {
        $connectionDefault = config('database.original');
        $databaseDefault = config("database.connections.{$connectionDefault}.database");

        $this->table = $databaseDefault . '.' . $this->table;
    }
}
