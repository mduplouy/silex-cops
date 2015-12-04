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
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Composer update script
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ComposerUpdate
{
    /**
     * Application instance
     * @var \Cops\Core\Application
     */
    private static $app;

    /**
     * Launch update (triggered after any composer install/update command)
     *
     * @param  Event $event
     *
     * @return void
     */
    public static function update(Event $event)
    {
        self::$app = require __DIR__ . '/../../../bootstrap.php';

        $output = new ConsoleOutput;

        try {
            self::initStorageDir();

            self::initDatabase($output);

        } catch (\Exception $e) {

            $output->writeln(sprintf(
                '%s Exception thrown in %s:%s',
                get_class($e),
                $e->getFile(),
                $e->getLine()
            ));

            $output->writeLn(sprintf(
                'Message was %s',
                $e->getMessage()
            ));

            $output->writeLn('Fix and relaunch "composer run-script post-update-cmd"');

            $output->writeln('');
        }
    }

    /**
     * Check and create internal database storage dir
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    private static function initStorageDir()
    {
        $targetDB = self::$app['config']->getInternalDatabasePath();
        $targetDir = dirname($targetDB);

        if (!file_exists($targetDB)) {

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (!is_dir($targetDir)) {
                throw new \RuntimeException(sprintf(
                    'Unable to create target dir %s please check permissions',
                    $targetDir
                ));
            }
        }
    }

    /**
     * Create internal DB if needed
     *
     * @param  ConsoleOutput $output
     *
     * @return void
     */
    private static function initDatabase(ConsoleOutput $output)
    {
        if (self::$app['repository.user']->createTable()) {
            $output->writeln('<info>Internal database setup : Ok !</info>');
            $output->writeln(
                sprintf(
                    '<info>Admin login set to %s : %s</info>',
                    self::$app['config']->getValue('default_login'),
                    self::$app['config']->getValue('default_password')
                )
            );
        }

        if (self::$app['repository.user-book']->createTable()) {
            $output->writeln('<info>User books read list table creation : Ok !</info>');
        }

        $output->writeln('Done');
    }
}
