<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

ini_set('date.timezone', 'Europe/Paris');

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// Define core model as a shared service
// Also loads and set configuration
$app['core'] = $app->share(function ($app) {
    return new \Cops\Model\Core($app, __DIR__.'/cops/config.ini');
});

// Load twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../themes/'.$app['core']->getConfig()->getValue('theme'),
));

// Load doctrine DBAL
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../'.$app['core']->getConfig()->getValue('data_dir').'/metadata.db',
    ),
));

// Set the mount points for the controllers
$app->mount('/', new \Cops\Controller\IndexController());
$app->mount('/book/', new \Cops\Controller\BookController());

return $app;