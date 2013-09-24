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
     * Resource instance
     * @var \Cops\Model\Resource
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
        $app->register(new \Cops\Provider\MobileDetectServiceProvider());

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

        // Register url generator service
        $app->register(new \Cops\Provider\UrlGeneratorServiceProvider());

        // Register translator
        $app->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => $app['config']->getValue('default_lang'),
        ));
        $app['translator'] = $app->share($app->extend('translator', function($translator) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', BASE_DIR.'locales/en.yml', 'en');
            $translator->addResource('yaml', BASE_DIR.'locales/fr.yml', 'fr');
            return $translator;
        }));

        // Set image adapters to create thumbnails
        $app['image_gd'] = function() {
            return new \Cops\Model\ImageProcessor\Adapter\Gd();
        };
        $app['image_imagick'] = function() {
            return new \Cops\Model\ImageProcessor\Adapter\Imagick();
        };

        self::$_app = $app;
    }

    /**
     * Simple object loader
     *
     * @param string $className
     * @param array $args
     *
     * @return \Cops\Model\Common
     */
    public function getModel($className, $args = array())
    {
        if (!isset($this->_objecInstance[$className])) {
            $fullClassName = $className;
            if (!class_exists($fullClassName)) {
                $fullClassName = __NAMESPACE__.'\\'.$className;
            }

            $obj = new \ReflectionClass($fullClassName);

            if (!is_array($args)) {
                $args = array($args);
            }
            $this->_objecInstance[$className] = $obj->newInstanceArgs($args);
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
        if (is_null($this->_resource)) {
            $this->_resource = $this->getModel(get_called_class().'\\Resource');
        }
        return $this->_resource;
    }

    /**
     * Collection object loader
     *
     * @return \Cops\Model\Collection
     */
    public function getCollection()
    {
        $fullClassName = get_called_class().'\\Collection';
        if (!class_exists($fullClassName)) {
            $fullClassName = __NAMESPACE__.'\\'.$className;
        }
        return new $fullClassName($this);
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
