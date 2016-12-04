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
 * Abstract database service provider for mysql / pgsql
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractDoctrineProvider implements ServiceProviderInterface
{
    /**
     * Doctrine driver name
     * @var string
     */
    private $driver;

    /**
     * Consructor
     *
     * @param string $driver
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @inheritDoc
     */
    public function register(Application $app)
    {
        $config = $app['config'];
        $host   = $config->getValue('db_host');
        $port   = $config->getValue('db_port');
        $user   = $config->getValue('db_user');
        $passwd = $config->getValue('db_password');

        $options = array();
        foreach ($app['config']->getValue('db_name') as $key => $dbName) {
            $options[$key] = array(
                'driver' => $this->driver,
                'dbname' => $dbName,
                'host'   => $host,
                'port'   => $port,
                'user'   => $user,
                'password' => $passwd,
            );
        }

        // Always add silexcops for internal storage
        $options[CopsApplication::INTERNAL_DB_KEY] = array(
            'driver' => $this->driver,
            'dbname' => $config->getValue('db_internal'),
            'host'   => $host,
            'port'   => $port,
            'user'   => $user,
            'password' => $passwd,
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
