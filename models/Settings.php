<?php namespace Techmobi\Multidb\Models;

use Model;
use System\Models\PluginVersion;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'techmobi_multidb_settings';

    public $settingsFields = 'fields.yaml';

    public function getDbNameOptions()
    {
        return [
            'hash' => 'techmobi.multidb::lang.options.hash',
            'domain' => 'techmobi.multidb::lang.options.domain',
        ];
    }

    public function getPluginsOptions()
    {
        return PluginVersion::lists('code', 'code');
    }
}
