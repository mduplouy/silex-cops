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

$params = array(
    'config-file' => __DIR__.'/src/config.ini'
);

return new \Cops\Core\Application($params);
