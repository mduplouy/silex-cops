<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

/**
 * Core class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Core
{
    /**
     * Model object instance registry
     * @var array
     */
    protected $_modelInstance = array();

    /**
     * Resource access
     * @var mixed
     */
    protected $_resource;

    /**
     * App instance
     * @var \Silex\Application
     */
    private static $_app;

    /**
     * Constructor
     *
     * @param \Silex\Application $app
     *
     * @param string $configFilePath
     */
    public function __construct(\Silex\Application $app, $configFilePath)
    {
        // Always instanciate configuration, so no closure use
        $app['config'] = new \Cops\Model\Config($configFilePath);

        if ($app['config']->getValue('debug')) {
            $app['debug'] = true;
        }

        // Register mobile detect service
        $app->register(new \Binfo\Silex\MobileDetectServiceProvider());

        // Detect mobile user agent
        if ($app['mobile_detect']->isMobile()) {
            $app['config']->setTemplatePrefix($app['config']->getValue('mobile_theme'));
        }

        // Register twig service
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => BASE_DIR.'themes/'.$app['config']->getValue('theme'),
        ));

        // Register doctrine DBAL service
        $app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_sqlite',
                'path'     => BASE_DIR.$app['config']->getValue('data_dir').'/metadata.db',
            ),
        ));

        self::$_app = $app;
    }

    /**
     * Simple object loader
     *
     * @param string $className
     *
     * @return \Cops\Model\Common
     */
    public function getModel($className)
    {
        if (!isset($this->_objecInstance[$className])) {
            $fullClassName = __NAMESPACE__.'\\'.$className;
            $this->_objecInstance[$className] = new $fullClassName;
        }
        return $this->_objecInstance[$className];
    }

    /**
     * Resource object loader
     *
     * @return \Cops\Model\Resource\Common
     */
    public function getResource()
    {
        if (empty($this->_resourceName)) {
            throw new \Exception('No resource name set');
        }
        if (is_null($this->_resource)) {
            $this->_resource = $this->getModel($this->_resourceName);
        }
        return $this->_resource;
    }

    /**
     * App getter
     *
     * @return \Silex\Application
     */
    public static function getApp()
    {
        return self::$_app;
    }

    /**
     * Config getter
     *
     * @return \Cops\Model\Config
     */
    public static function getConfig()
    {
        $app = self::getApp();
        return $app['config'];
    }

    /**
     * DBAL connection getter
     *
     * @return Doctrine\DBAL\Connection
     */
    public static function getDb()
    {
        $app = self::getApp();
        return $app['db'];
    }

}