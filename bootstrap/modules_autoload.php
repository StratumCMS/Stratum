<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

$modulesDir = __DIR__ . '/../modules';

foreach (scandir($modulesDir) as $dir) {
    if ($dir === '.' || $dir === '..') continue;

    $moduleSrc = $modulesDir . '/' . $dir . '/src';

    if (is_dir($moduleSrc)) {
        $namespace = 'Modules\\' . ucfirst($dir) . '\\';
        $loader->addPsr4($namespace, $moduleSrc);
    }
}
