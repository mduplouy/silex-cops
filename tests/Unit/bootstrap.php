<?php

define('BASE_DIR', __DIR__.'/../../');
define('DS', DIRECTORY_SEPARATOR);

$loader = require BASE_DIR.'vendor/autoload.php';
$loader->add('Cops\Tests', __DIR__);

$app = new \Cops\Model\Application();

// Define core model, no closure to ensure loading
// Load configuration & set service providers
$app['core'] =  new \Cops\Model\Core(BASE_DIR.'app/cops/config.ini', $app);

$app['debug'] = true;

// Register special database for tests
$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__ . '/database.db',
    ),
));

return $app;
