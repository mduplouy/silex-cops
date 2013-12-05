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

use Cops\Model\CoreInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Core class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Core implements CoreInterface
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
    public function __construct($configFilePath, Application $app)
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
            'twig.path' => array(
                BASE_DIR.'themes/'.$app['config']->getValue('theme'),
                __DIR__.'/../Templates',
            )
        ));

        // Register doctrine DBAL service
        $app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_sqlite',
                'path'     => BASE_DIR.$app['config']->getValue('data_dir').'/metadata.db',
            ),
        ));

        // Register security provider
        $app->register(new \Silex\Provider\SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'admin' => array(
                    'pattern' => '^/admin/',
                    'http' => true,
                    'users' => array(
                        'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==')
                    )
                ),
            )
        ));

        // Register session provider
        $app->register(new \Silex\Provider\SessionServiceProvider());

        // Register url generator service
        $app->register(new \Cops\Provider\UrlGeneratorServiceProvider());

        // Register translator
        $app->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => $app['config']->getValue('default_lang'),
        ));
        $app['translator'] = $app->share($app->extend('translator', function($translator) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', BASE_DIR.'locales/fr.yml', 'fr');
            return $translator;
        }));

        // Set the mount points for the controllers
        $app->mount('/',           new \Cops\Controller\IndexController());
        $app->mount('book/',       new \Cops\Controller\BookController());
        $app->mount('serie/',      new \Cops\Controller\SerieController());
        $app->mount('author/',     new \Cops\Controller\AuthorController());

        $app->mount('admin/',      new \Cops\Controller\AdminController());
        $app->mount('admin/feed/', new \Cops\Controller\Admin\OpdsFeedController());

        $app->mount('login/',      new \Cops\Controller\LoginController());
        $app->mount('opds/',       new \Cops\Controller\OpdsController());

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
            return $obj->newInstanceArgs($args);
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
            $fullClassName = __NAMESPACE__.'\\'.$fullClassName;
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

    /**
     * Get alphabetic letters
     *
     * @return array
     */
    public static function getLetters()
    {
        return array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        );
    }

    /**
     * Remove accent from a string
     */
    public function removeAccents($str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        return $str;
    }

    /**
     * Empty properties on clone
     */
    public function __clone()
    {
        $this->_objecInstance = array();
        $this->_resource = null;
    }
}
