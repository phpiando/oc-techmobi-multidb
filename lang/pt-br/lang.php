<?php return [
    'plugin' => [
        'name' => 'MultiDB',
        'description' => '',
    ],
    'labels' => [
        'prefix_db' => 'Prefixo do Banco',
        'db_name' => 'Nome do Banco',
        'has_user_db' => 'Informar usuário responsável em gerir o banco de dados?',
        'db_host' => 'Host',
        'db_port' => 'Porta',
        'db_user' => 'Usuário',
        'db_pass' => 'Senha',
        'plugins' => 'Plugins',
        'name' => 'Descrição',
        'domains' => 'Domínios',
        'has_sync_update' => 'Receber atualizações quando houver alterações nas migrações?',
    ],
    'options' => [
        'hash' => 'Hash',
        'domain' => 'Nome com base no Dominio',
    ],
    'comments' => [
        'has_user_db' => 'Se preferir, você poderá informar um usuário para gerir a criação dos bancos de dados, lembrando que é necessário ter permissões Leitura, Escrita e Atualização.',
        'plugins' => 'Informe quais plugins que serão utilizados nas migrações dos bancos de dados',
    ],
    'list' => [
        'force_sync_all_comment' => 'Deseja atualizar todos bancos de dados registrados?',
        'force_sync_all' => 'Atualizar todos bancos',
        'force_sync_selected' => 'Atualizar bancos selecionados',
        'force_sync_selected_comment' => 'Deseja atualizar os bancos selecionados?',
        'update_success' => 'Bancos de dados atualizados com sucesso',
    ],
    'settings' => [
        'tab' => 'MultiDB',
        'geral' => 'Configurações Plugin',
        'geral_details' => 'Personalize os bancos de dados do MultiDB',
        'domains' => 'MultiDB Dominios',
        'domains_details' => 'Gerencie os bancos de dados com base nos dominios',
    ],
];
