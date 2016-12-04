<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Core\Provider\Database;

use Silex\Application;
use Cops\Core\Application as CopsApplication;
use Silex\ServiceProviderInterface;
use Silex\Provider\DoctrineServiceProvider;

/**
 * SQLite database service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SqliteProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Application $app)
    {
        $options = array();
        foreach ($app['config']->getValue('db_name') as $key => $path) {
            $options[$key] = array(
                'driver' => 'pdo_sqlite',
                'path' =>  $app['config']->getDatabasePath($key) . '/metadata.db',
                'driverOptions' => $app['calibre-util']->getDBInternalFunctions(),
            );
        }

        // Always add silexcops for internal storage
        $options[CopsApplication::INTERNAL_DB_KEY] = array(
            'driver' => 'pdo_sqlite',
            'path' => $app['config']->getInternalDatabasePath(),
        );

        // Register doctrine DBAL service
        $app->register(new DoctrineServiceProvider(), array(
            'dbs.options' => $options
        ));

        return $app;
    }

    /**
     * @inheritDoc
     */
    public function boot(Application $app)
    {
    }
}