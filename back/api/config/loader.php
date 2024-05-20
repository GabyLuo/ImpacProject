<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$dirs = [
    $config->application->modelsDir,
    $config->application->controllersDir,
    $config->application->libDir,
    $config->application->vendorDir,
];

$prefixes = ['sys/', 'vnt/', 'pmo/', 'fin/', 'inv/', 'pro/', 'exp/', 'lic/', 'per/', 'rpt/', 'com/', 'crm/', 'cmp/']; // ...

foreach ($prefixes as $pfx) {
    $dirs[] = $config->application->modelsDir . $pfx;
    $dirs[] = $config->application->controllersDir . $pfx;
}

$loader->registerDirs(
    $dirs
)->register();