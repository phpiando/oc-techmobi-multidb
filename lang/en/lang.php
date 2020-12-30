<?php return [
    'plugin' => [
        'name' => 'MultiDB',
        'description' => '',
    ],
    'labels' => [
        'prefix_db' => 'Prefix Database',
        'db_name' => 'Database name',
        'has_user_db' => 'Identifier user for managing the database',
        'db_host' => 'Host',
        'db_port' => 'Port',
        'db_user' => 'User',
        'db_pass' => 'Pass',
        'plugins' => 'Plugins',
        'name' => 'Description',
        'domains' => 'Hosts',
        'has_sync_update' => 'Receive updates when there are changes in migrations?',
    ],
    'options' => [
        'hash' => 'Hash',
        'domain' => 'Name with base in domain',
    ],
    'comments' => [
        'has_user_db' => 'If you prefer, you can inform a user to manage the creation of the databases, remembering that it is necessary to have Read, Write and Update permissions.',
        'plugins' => 'Inform which plugins will be used for database migrations',
    ],
    'list' => [
        'force_sync_all_comment' => 'Do you want to update all registered databases?',
        'force_sync_all' => 'Update all databases',
        'force_sync_selected' => 'Update selected database',
        'force_sync_selected_comment' => 'Do you want to update the selected database?',
        'update_success' => 'Database updated success',
    ],
    'settings' => [
        'tab' => 'MultiDB',
        'geral' => 'Settings Plugin',
        'geral_details' => 'Customize MultiDB databases',
        'domains' => 'MultiDB Hosts',
        'domains_details' => 'Manage databases based on domains',
    ],
];
