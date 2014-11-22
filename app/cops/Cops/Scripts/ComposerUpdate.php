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
use Symfony\Component\Console\Output\ConsoleOutput;
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
        $targetDB = self::$app['config']->getInternalDatabasePath();
        $targetDir = dirname($targetDB);

        if (!file_exists($targetDB)) {
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $output = new ConsoleOutput;
            $output->writeln('');

            if (is_dir($targetDir)) {
                self::$app['model.user']->getResource()->initTable();

                $output->writeln('<info>Internal database setup : Done !</info>');
                $output->writeln(
                    sprintf(
                        '<info>Admin login set to %s : %s</info>',
                        self::$app['config']->getValue('default_login'),
                        self::$app['config']->getValue('default_password')
                    )
                );
                $output->writeln('');

                return;
            }

            $output->writeln(
                sprintf(
                    '<error> Unable to create directory %s, please check permissions, then run "composer run-script post-update-cmd" command </error>',
                    realpath($targetDB)
                )
            );
            $output->writeln('');

        }
    }
}