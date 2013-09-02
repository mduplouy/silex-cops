<?php

ini_set('date.timezone', 'Europe/Paris');

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

// Parse configuration
$app['config'] = parse_ini_file(__DIR__.'/cops/config.ini', true);

// Load doctrine DBAL
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../data/metadata.db',
    ),
));

// Load twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../themes/default',
));

$app->mount('/', new \Cops\Controller\IndexController());
$app->mount('/book/', new \Cops\Controller\BookController());

return $app;