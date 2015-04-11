<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Core\Provider;

use Silex\Application;
use Cops\Core\Application as CopsApplication;
use Silex\ServiceProviderInterface;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * Mobile Detect service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Application $app)
    {
        $options = array();
        foreach ($app['config']->getValue('data_dir') as $key => $path) {
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

        // Set the callback to change database on the fly
        $app->before(function (Request $request) use($app) {
            try {
                $app['config']->setDatabaseKey($app, $request->get('database', null));
            } catch (\InvalidArgumentException $e) {
                $app->abort(404, 'Inexistant database');
            }
        });

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
