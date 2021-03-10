<?php namespace Techmobi\Multidb\Models;

use Keios\Multisite\Models\Setting as SettingMultisite;
use Model;
use Techmobi\Multidb\Classes\SyncDB;
use Techmobi\Multidb\Models\Settings;

/**
 * Model
 */
class Domain extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'techmobi_multidb_domains';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
    ];

    public $belongsToMany = [
        'sites' => [
            SettingMultisite::class,
            'table' => 'techmobi_multidb_multisites',
            'otherKey' => 'site_id',
        ],
    ];

    public function beforeCreate()
    {
        $dbName = trim(Settings::get('prefix_db')) . '';

        if (Settings::get('db_name') == 'hash') {
            $dbName .= uniqid();
        } else {
            $dbName .= str_slug($this->name, "_");
        }

        $this->db_name = $dbName;
    }

    public function afterSave()
    {
        SyncDB::instance()->startSyncDB($this);
    }

    public function scopeHasSyncUpdate($query)
    {
        return $query->where('has_sync_update', 1);
    }

    public function getSitesOptions()
    {
        return SettingMultisite::lists('domain', 'id');
    }
}
