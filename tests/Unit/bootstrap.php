<?php

define('BASE_DIR', __DIR__.'/../../');
define('DS', DIRECTORY_SEPARATOR);
define('DATABASE', __DIR__ . '/../data/metadata.db');

$loader = require BASE_DIR.'vendor/autoload.php';
$loader->add('Cops\Tests', __DIR__);

return require __DIR__.'/application.php';
