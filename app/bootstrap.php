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
define('DS', DIRECTORY_SEPARATOR);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// Define core model, no closure to ensure loading
// Load configuration & set service providers
$app['core'] =  new Cops\Model\Core(__DIR__.'/cops/config.ini', $app);

// Set the mount points for the controllers
$app->mount('/',       new Cops\Controller\IndexController());
$app->mount('book/',   new Cops\Controller\BookController());
$app->mount('serie/',  new Cops\Controller\SerieController());
$app->mount('author/', new Cops\Controller\AuthorController());

return $app;