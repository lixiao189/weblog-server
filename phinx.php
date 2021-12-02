<?php
// 载入配置文件
$dotenv = \Dotenv\Dotenv::createImmutable(base_path());
$dotenv->safeLoad();

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => $_ENV['db_host'],
            'name' => $_ENV['db_database'],
            'user' => $_ENV['db_username'],
            'pass' => $_ENV['db_password'],
            'port' => $_ENV['db_port'],
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => $_ENV['db_host'],
            'name' => $_ENV['db_database'],
            'user' => $_ENV['db_username'],
            'pass' => $_ENV['db_password'],
            'port' => $_ENV['db_port'],
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
