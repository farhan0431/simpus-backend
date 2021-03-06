<?php
return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'migrations' => 'migrations',
    'connections' => [
			'oracle' => [
				'driver'        => 'oracle',
				'tns'           => env('DB_TNS_OR', ''),
				'host'          => env('DB_HOST_OR', ''),
				'port'          => env('DB_PORT_OR', '1521'),
				'database'      => env('DB_DATABASE_OR', ''),
				'username'      => env('DB_USERNAME_OR', ''),
				'password'      => env('DB_PASSWORD_OR', ''),
				'charset'       => env('DB_CHARSET_OR', 'AL32UTF8'),
				'prefix'        => env('DB_PREFIX_OR', ''),
				'prefix_schema' => env('DB_SCHEMA_PREFIX_OR', ''),
			],
			'mysql' => [
				'driver' => 'mysql',
				'url' => env('DATABASE_URL'),
				'host' => env('DB_HOST', '127.0.0.1'),
				'port' => env('DB_PORT', '3306'),
				'database' => env('DB_DATABASE', 'forge'),
				'username' => env('DB_USERNAME', 'forge'),
				'password' => env('DB_PASSWORD', ''),
				'unix_socket' => env('DB_SOCKET', ''),
				'charset' => 'utf8mb4',
				'collation' => 'utf8mb4_unicode_ci',
				'prefix' => '',
				'prefix_indexes' => true,
				'strict' => true,
				'engine' => null,
				'options' => extension_loaded('pdo_mysql') ? array_filter([
					PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
				]) : [],
			],
			'mysql_bphtb' => [
				'driver' => 'mysql',
				'url' => env('DATABASE_URL'),
				'host' => env('DB_HOST', '127.0.0.1'),
				'port' => env('DB_PORT', '3306'),
				'database' => env('DB_DATABASE_BPHTB', 'forge'),
				'username' => env('DB_USERNAME', 'forge'),
				'password' => env('DB_PASSWORD', ''),
				'unix_socket' => env('DB_SOCKET', ''),
				'charset' => 'utf8mb4',
				'collation' => 'utf8mb4_unicode_ci',
				'prefix' => '',
				'prefix_indexes' => true,
				'strict' => true,
				'engine' => null,
				'options' => extension_loaded('pdo_mysql') ? array_filter([
					PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
				]) : [],
			],
			'mysql_simpad' => [
				'driver' => 'mysql',
				'url' => env('DATABASE_URL'),
				'host' => env('DB_HOST', '127.0.0.1'),
				'port' => env('DB_PORT', '3306'),
				'database' => env('DB_DATABASE_SIMPAD', 'forge'),
				'username' => env('DB_USERNAME', 'forge'),
				'password' => env('DB_PASSWORD', ''),
				'unix_socket' => env('DB_SOCKET', ''),
				'charset' => 'utf8mb4',
				'collation' => 'utf8mb4_unicode_ci',
				'prefix' => '',
				'prefix_indexes' => true,
				'strict' => true,
				'engine' => null,
				'options' => extension_loaded('pdo_mysql') ? array_filter([
					PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
				]) : [],
			],
    ],
];