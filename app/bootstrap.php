<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define('BASE_DIR', __DIR__.'/../');
define('DS', DIRECTORY_SEPARATOR);

require_once BASE_DIR.'/vendor/autoload.php';

$app = new \Silex\Application();

// Load & set configuration
$app['config'] = new \Cops\Model\Config(BASE_DIR.'app/cops/config.ini', new \Cops\Model\Utils);

if ($app['config']->getValue('debug')) {
    $app['debug'] = true;
}

// Define core model
$app['core'] =  new \Cops\Model\Core($app);

return $app;