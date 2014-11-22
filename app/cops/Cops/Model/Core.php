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
use Cops\Provider\MobileDetectServiceProvider;
use Cops\Provider\UrlGeneratorServiceProvider;
use Cops\Provider\ImageProcessorServiceProvider;
use Cops\Provider\TranslationServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Cops\EventListener\LocaleListener;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Core class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Core
{
    /**
     * Resource instance
     * @var \Cops\Model\Resource
     */
    protected $resource;

    /**
     * App instance
     * @var Application
     */
    private static $app;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->registerServices($app);

        $app->get('/', function () use ($app) {
            // redirect to /default_lang/
            $redirect = $app['url_generator']->generate('homepage', array(
                '_locale'  => $app['config']->getValue('default_lang'),
                'database' => $app['config']->getValue('default_database_key'),
            ));
            return $app->redirect($redirect, 301);
        });

        // Not used yet, but prefix is not needed here
        $app->mount('/login/',               new \Cops\Controller\LoginController($app));

        // Admin related controllers
        $adminPath = $app['config']->getAdminPath();
        // Keep these up to avoid side effects on admin panel
        $app->mount($adminPath.'/{_locale}',           new \Cops\Controller\AdminController($app));
        $app->mount($adminPath.'/{_locale}/database/', new \Cops\Controller\Admin\DatabaseController($app));
        $app->mount($adminPath.'/{_locale}/users/',    new \Cops\Controller\Admin\UserController($app));
        $app->mount($adminPath.'/{_locale}/feed/',     new \Cops\Controller\Admin\OpdsFeedController($app));

        // Set the mount points for the controllers with database prefix
        $app->mount('{database}/{_locale}/',            new \Cops\Controller\IndexController($app));

        $app->mount(
            '{database}/{_locale}/book/',
            new \Cops\Controller\BookController($app)
        );

        $app->mount(
            '{database}/{_locale}/serie/',
            new \Cops\Controller\SerieController($app)
        );

        $app->mount(
            '{database}/{_locale}/author/',
            new \Cops\Controller\AuthorController($app)
        );

        $app->mount(
            '{database}/{_locale}/tag/',
            new \Cops\Controller\TagController($app)
        );

        $app->mount(
            '{database}/{_locale}/search/',
            new \Cops\Controller\SearchController($app)
        );

        $app->mount(
            '{database}/{_locale}/inline-edit/',
            new \Cops\Controller\InlineEditController($app)
        );

        $app->mount(
            '{database}/{_locale}/opds/',
            new \Cops\Controller\OpdsController($app)
        );

        $app['core'] = $this;

        self::$app = $app;
    }

    /**
     * Register the various app services
     *
     * @param Application $app
     *
     * @return void
     */
    private function registerServices(Application $app)
    {
        $app
            ->register(new MobileDetectServiceProvider())    // Register mobile detect service
            ->register(new SessionServiceProvider)           // Register session provider
            ->register(new UrlGeneratorServiceProvider)      // Register url generator service
            ->register(new TwigServiceProvider(), array(     // Register twig service
                'twig.path' => array(
                    BASE_DIR . 'themes/' . $app['config']->getValue('theme'),
                    __DIR__ . '/../Templates',
                ),
                'twig.options' => array(
                    'cache' => realpath(BASE_DIR . 'cache'),
                )
            ))
            ->register(new FormServiceProvider())
            ->register(new ValidatorServiceProvider())
            // Translator
            ->register(new TranslationServiceProvider(array(
                'default' => $app['config']->getValue('default_lang')
            )));

        // Detect mobile user agent
        if ($app['mobile_detect']->isMobile()) {
            $app['config']->setTemplatePrefix($app['config']->getValue('mobile_theme'));
        }

        $app['translator'] = $app->share($app->extend('translator', function($translator) {
            $translator->addLoader('yaml', new YamlFileLoader());

            foreach (array('messages', 'admin', 'validators') as $domain) {
                $translator->addResource('yaml', BASE_DIR.'locales/fr/'.$domain.'.yml', 'fr', $domain);
                $translator->addResource('yaml', BASE_DIR.'locales/en/'.$domain.'.yml', 'en', $domain);
            }

            return $translator;
        }));

        // Remove any file marked as "to be deleted"
        $app->finish(function (Request $request, Response $response) use ($app) {
            if (isset($app['delete_file']) && PHP_SAPI != 'cli') {
                unlink($app['delete_file']);
            }
        });

        $this
            ->registerDatabaseService($app)     // Load databases
            ->registerModels($app)              // Register models in DIC
            ->registerSecurityService($app)     // Security setup
            ->registerConsoleCommands($app);    // Console commands
    }

    /**
     * Register database service and try to load any defined database
     *
     * @param  Application $app
     *
     * @return $this
     */
    private function registerDatabaseService(Application $app)
    {
        $options = array();
        foreach ($app['config']->getValue('data_dir') as $key => $path) {

            $options[$key] = array(
                'driver' => 'pdo_sqlite',
                'path' =>  $app['config']->getDatabasePath($key) . '/metadata.db',
                'driverOptions' => Calibre::getDBInternalFunctions(),
            );
        }

        // Always add silexcops for internal storage
        $options['silexCops'] = array(
            'driver' => 'pdo_sqlite',
            'path' => $app['config']->getInternalDatabasePath(),
        );

        // Register doctrine DBAL service
        $app->register(new DoctrineServiceProvider(), array(
            'dbs.options' => $options
        ));

        // Set the callback to change database on the fly
        $app->before(function(Request $request) use($app) {
            try {

                if (!$dbKey = $request->get('database')) {
                    $dbKey = $app['config']->getValue('default_database_key');
                }

                $configuredDatabases = $app['config']->getValue('data_dir');
                if (!array_key_exists($dbKey, $configuredDatabases)) {
                    throw new \InvalidArgumentException('Database does not exist');
                }

                $app['db'] = $app->share($app->extend('db', function($db, $app) use($dbKey) {
                    return $app['dbs'][$dbKey];
                }));

                $app['config']->setValue('current_database_key', $dbKey);
                $app['config']->setValue('current_database_path', $app['config']->getDatabasePath($dbKey));

            } catch (\InvalidArgumentException $e) {
                $app->abort(404, 'Inexistant database');
            }
        });

        return $this;
    }

    /**
     * Dedicated method to register security service
     *
     * @param  Application $app
     *
     * @return $this
     */
    private function registerSecurityService(Application $app)
    {
        $app['provider.user'] = function($app) {
            return new \Cops\Model\User\Provider($app['model.user']);
        };

        // Register security provider
        $app->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'admin' => array(
                    'pattern' => '^/admin',
                    'http' => true,
                    'users' => $app['provider.user'],
                ),
                'default' => array(
                    'pattern' => '^.*$',
                    'http' => true,
                    'users' => $app['provider.user'],
                ),
            )
        ));

        $app['security.role_hierarchy'] = array(
            'ROLE_ADMIN' => array('ROLE_USER','ROLE_EDIT'),
            'ROLE_EDIT'  => array('ROLE_USER'),
        );

        $accessRules = array();
        foreach($app['config']->getValue('data_dir') as $urlPrefix => $dataPath) {
            $accessRules[] = array('^/../'.$urlPrefix.'/admin',       'ROLE_ADMIN');
            $accessRules[] = array('^/../'.$urlPrefix.'/inline-edit', 'ROLE_EDIT');
        }
        $app['security.access_rules'] = $accessRules;

        return $this;
    }

    /**
     * Register console commands (cache warmup etc..)
     *
     * @param  Application $app
     *
     * @return $this
     */
    private function registerConsoleCommands(Application $app)
    {
        // Register console
        $app->register(new \LExpress\Silex\ConsoleServiceProvider(), array(
            'console.name'    => 'SilexCops',
            'console.version' => '1.0',
        ));

        $app['command.cache-warmup'] = $app->share(function ($app) {
            return new \Cops\Command\GenerateThumbnails('generate:thumbnails', $app);
        });
        $app['command.init-database'] = $app->share(function ($app) {
            return new \Cops\Command\InitDatabase('generate:thumbnails', $app);
        });

        return $this;
    }

    /**
     * Register models in DIC
     *
     * @param  Application $app
     *
     * @return $this
     */
    private function registerModels(Application $app)
    {
        $app['utils'] = $app->share(function(){
            return new \Cops\Model\Utils;
        });
        $app['model.book'] = function($app) {
            return new \Cops\Model\Book($app);
        };
        $app['model.author'] = function($app) {
            return new \Cops\Model\Author($app);
        };
        $app['model.serie'] = function($app) {
            return new \Cops\Model\Serie($app);
        };
        $app['model.tag'] = function($app) {
            return new \Cops\Model\Tag($app);
        };
        $app['model.cover']  = $app->share(function($app) {
             return new \Cops\Model\Cover($app);
        });
        $app['model.bookfile'] = function($app) {
            return new \Cops\Model\BookFile($app);
        };
        $app['model.user'] = function($app) {
            return new \Cops\Model\User($app);
        };
         // Calibre internal routines (author_sort etc..)
        $app['model.calibre'] = $app->share(function ($app) {
            return new \Cops\Model\Calibre($app);
        });
        // Form class
        $app['form.type.user'] = function($app) {
            return new \Cops\Form\Type\UserType;
        };

        // Factories
        $app['factory.bookfile'] = $app->share(function($app) {
            return new \Cops\Model\BookFile\BookFileFactory($app);
        });
        $app['factory.archive'] = $app->share(function($app) {
            return new \Cops\Model\Archive\ArchiveFactory($app);
        });
        $app['factory.search'] = $app->share(function ($app) {
            return new \Cops\Model\Search\SearchFactory($app);
        });
        $app['factory.image'] = $app->share(function ($app) {
            return new \Cops\Model\ImageProcessor\ImageProcessorFactory($app);
        });
        return $this;
    }
}