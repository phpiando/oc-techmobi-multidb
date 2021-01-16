<?php namespace Techmobi\Multidb\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Flash;
use System\Classes\SettingsManager;
use Techmobi\Multidb\Models\Domain;

class Domains extends Controller
{
    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Techmobi.Multidb', 'multidb_domains');
    }

    public function onForceUpdateSelected()
    {
        $ids = post('checked');

        foreach ($ids as $key => $value) {
            $model = Domain::find($value);
            SyncDB::instance()->startSyncDB($model);
        }

        Flash::success(trans('techmobi.multidb::lang.list.update_success'));
    }

    public function onForceUpdateAll()
    {
        $domains = Domain::get();

        foreach ($domains as $model) {
            SyncDB::instance()->startSyncDB($model);
        }

        Flash::success(trans('techmobi.multidb::lang.list.update_success'));
    }
}
