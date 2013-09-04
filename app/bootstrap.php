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
define('BASE_DIR', __DIR__.'/../');

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

// Define core model
// Load configuration & set service providers
$app['core'] =  new Cops\Model\Core($app, __DIR__.'/cops/config.ini');

// Set the mount points for the controllers
$app->mount('/', new Cops\Controller\IndexController());
$app->mount('book/', new Cops\Controller\BookController());

// Register url generator service
$app->register(new Cops\Provider\UrlGeneratorServiceProvider());

return $app;