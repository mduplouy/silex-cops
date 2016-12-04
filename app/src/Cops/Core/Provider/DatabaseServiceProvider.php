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
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Database service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * Adapter mapping for different database types
     * @var array
     */
    private $adapterMapping = array(
        'sqlite' => '\Cops\Core\Provider\Database\SqliteProvider',
        'mysql'  => '\Cops\Core\Provider\Database\MysqlProvider',
    );


    /**
     * @inheritDoc
     *
     * @throws \UnexpectedValueException
     */
    public function register(Application $app)
    {
        $dbEngine = $app['config']->getValue('db_engine');

        if (!array_key_exists($dbEngine, $this->adapterMapping)) {
            throw new \UnexpectedValueException(sprintf(
                'Invalid database engine : %s must be one of %',
                $dbEngine,
                implode(', ', array_keys($this->adapterMapping))
            ));
        }

        $app->register(new $this->adapterMapping[$dbEngine]);

        // Set the callback to change database on the fly
        $app->before(function (Request $request) use($app) {
            try {
                $app['config']->setDatabaseKey($app, $request->get('database', null));
            } catch (\InvalidArgumentException $e) {
                $app->abort(404, 'Inexistant database');
            }
        });

        return $app;
    }

    /**
     * @inheritDoc
     */
    public function boot(Application $app)
    {
    }
}
