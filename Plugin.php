<?php namespace Techmobi\Multidb;

use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = [
        'Keios.Multisite',
    ];

    public function register()
    {
        $this->registerConsoleCommand('multidb:dbcreate', 'Techmobi\Multidb\Console\DBCreate');
    }

    public function registerComponents() {}

    public function registerSettings()
    {
        return [
            'multidb_settings' => [
                'label' => 'techmobi.multidb::lang.settings.geral',
                'description' => 'techmobi.multidb::lang.settings.geral_details',
                'category' => 'techmobi.multidb::lang.settings.tab',
                'icon' => 'icon-cubes',
                'class' => 'Techmobi\Multidb\Models\Settings',
                'permissions' => ['techmobi.multidb.access_settings'],
                'order' => 400,
                'keywords' => 'multidb techmobi settings',
            ],
            'multidb_domains' => [
                'label' => 'techmobi.multidb::lang.settings.domains',
                'description' => 'techmobi.multidb::lang.settings.domains_details',
                'category' => 'techmobi.multidb::lang.settings.tab',
                'icon' => 'icon-globe',
                'url' => Backend::url('techmobi/multidb/domains'),
                'permissions' => ['techmobi.multidb.access_domains'],
                'order' => 500,
                'keywords' => 'multidb domains themes',
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'techmobi.multidb.access_settings' => [
                'tab' => 'techmobi.multidb::lang.settings.tab',
                'label' => 'Access Settings',
            ],
            'techmobi.multidb.access_domains' => [
                'tab' => 'techmobi.multidb::lang.settings.tab',
                'label' => 'Settings Domains',
            ],
        ];
    }
}
