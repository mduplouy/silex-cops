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

use Cops\EventListener\LocaleListener;
use Silex\Application as BaseApplication;

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
     * @var \Silex\Application
     */
    private static $app;

    /**
     * Constructor
     *
     * @param \Silex\Application $app
     */
    public function __construct(BaseApplication $app)
    {
        $this->registerServices($app);

        $app->get('/', function () use ($app) {
            // redirect to /default_lang/
            $redirect = $app['url_generator']->generate('homepage', array(
                '_locale' => $app['config']->getValue('default_lang')
            ));
            return $app->redirect($redirect, 301);
        });

        // Set the mount points for the controllers
        $app->mount('/',                     new \Cops\Controller\IndexController($app));
        $app->mount('/book/',                new \Cops\Controller\BookController($app));
        $app->mount('/serie/',               new \Cops\Controller\SerieController($app));
        $app->mount('/author/',              new \Cops\Controller\AuthorController($app));
        $app->mount('/tag/',                 new \Cops\Controller\TagController($app));
        $app->mount('/search/',              new \Cops\Controller\SearchController($app));

        $app->mount('/inline-edit/',         new \Cops\Controller\InlineEditController($app));

        $app->mount('/login/',               new \Cops\Controller\LoginController($app));
        $app->mount('/opds/',                new \Cops\Controller\OpdsController($app));

        $adminPath = $app['config']->getAdminPath();
        $app->mount($adminPath,              new \Cops\Controller\AdminController($app));
        $app->mount($adminPath.'/database/', new \Cops\Controller\Admin\DatabaseController($app));
        $app->mount($adminPath.'/feed/',     new \Cops\Controller\Admin\OpdsFeedController($app));

        // Set default storage dir
        if (!isset($app['book_storage_dir'])) {
            $app['book_storage_dir'] = BASE_DIR.$app['config']->getValue('data_dir');
        }

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

            foreach (array('messages', 'admin') as $domain) {
                $translator->addResource('yaml', BASE_DIR.'locales/fr/'.$domain.'.yml', 'fr', $domain);
                $translator->addResource('yaml', BASE_DIR.'locales/en/'.$domain.'.yml', 'en', $domain);
            }

            return $translator;
        }));

       // Register doctrine DBAL service
        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'        => 'pdo_sqlite',
                'path'          => BASE_DIR . $app['config']->getValue('data_dir') . '/metadata.db',
                'driverOptions' => Calibre::getDBInternalFunctions(),
            ),
        ));

        // Remove any file marked as "to be deleted"
        $app->finish(function (Request $request, Response $response) use ($app) {
            if (isset($app['delete_file']) && php_sapi_name() != 'cli') {
                unlink($app['delete_file']);
            }
        });

        $this
            ->registerSecurityService($app)     // Security setup
            ->registerConsoleCommands($app)     // Console commands
            ->registerModels($app);             // Register models in DIC
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
        // password encoding reminder
        // echo $app['security.encoder.digest']->encodePassword('password', '');

        // Register security provider
        $app->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'admin' => array(
                    'pattern' => '^/admin',
                    'http' => true,
                    'users' => array(
                        // admin : password
                        'admin' => array('ROLE_ADMIN', 'BFEQkknI/c+Nd7BaG7AaiyTfUFby/pkMHy3UsYqKqDcmvHoPRX/ame9TnVuOV2GrBH0JK9g4koW+CgTYI9mK+w==')
                    )
                ),
                'default' => array(
                    'pattern' => '^.*$',
                    'http' => true,
                    'users' => array(
                        // user : password
                        'user' => array('ROLE_EDIT', 'BFEQkknI/c+Nd7BaG7AaiyTfUFby/pkMHy3UsYqKqDcmvHoPRX/ame9TnVuOV2GrBH0JK9g4koW+CgTYI9mK+w=='),
                        // admin : password
                        'admin' => array('ROLE_ADMIN', 'BFEQkknI/c+Nd7BaG7AaiyTfUFby/pkMHy3UsYqKqDcmvHoPRX/ame9TnVuOV2GrBH0JK9g4koW+CgTYI9mK+w==')
                    )
                ),
            )
        ));

        $app['security.role_hierarchy'] = array(
            'ROLE_ADMIN' => array('ROLE_USER','ROLE_EDIT'),
            'ROLE_EDIT'  => array('ROLE_USER'),
        );

        $app['security.access_rules'] = array(
             array('^/../admin',        'ROLE_ADMIN'),
             array('^/../inline-edit/', 'ROLE_EDIT')
        );

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
            'console.version' => '0.0.5',
        ));

        $app['command.cache-warmup'] = $app->share(function ($app) {
            return new \Cops\Command\GenerateThumbnails('generate:thumbnails', $app);
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
         // Calibre internal routines (author_sort etc..)
        $app['model.calibre'] = $app->share(function ($app) {
            return new \Cops\Model\Calibre($app);
        });

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

    /**
     * App getter
     *
     * @return \Silex\Application
     */
    public static function getApp()
    {
        return self::$app;
    }
}