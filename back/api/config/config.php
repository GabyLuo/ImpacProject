<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

use Phalcon\Config\Adapter\Ini as ConfigIni;

$config = new ConfigIni(__DIR__ . '/../config.ini');

return new \Phalcon\Config([
    'database' => [
        'adapter'    => $config->database->adapter,
        'host'       => $config->database->host,
        'username'   => $config->database->username,
        'password'   => $config->database->password,
        'dbname'     => $config->database->dbname,
        //'charset'    => 'utf8', # Corregir problemas con postgres
        'options' => [
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_PERSISTENT => FALSE,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    ],

    'application' => [
        'modelsDir'      => APP_PATH . '/app/models/',
        'libDir'      => APP_PATH . '/lib/',
        'vendorDir'      => APP_PATH . '/lib/vendor/',
        'controllersDir'      => APP_PATH . '/app/controllers/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'baseUri'        => '/',
    ],

    'jwtkey' => 'Bk8m+/DV3CSvY5WTyeciZsBI4YzT6z3++ht1j90kHhRcw0+Vq7/NFa1/GiwEycPUHyGATmO/0AtLoni7unoXdQ==',
]);
