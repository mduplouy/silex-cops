<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Scripts;

use Composer\Script\Event;
use Silex\Application;
use Symfony\Component\Console\Input\ArrayInput;
/**
 * Composer update script
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ComposerUpdate
{
    /**
     * Application instance
     * @var Application
     */
    private static $app;

    /**
     * Launch update (triggered after any composer install/update command)
     *
     * @return void
     */
    public static function update(Event $event)
    {
        self::$app = require __DIR__ . '/../../../bootstrap.php';

        self::databaseUpdate();
    }

    /**
     * Make any needed change to internal database
     *
     * @return void
     */
    private static function databaseUpdate()
    {
        self::$app['model.user']->initTable();
    }
}