<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core;

use Silex\Application as BaseApplication;
use Cops\Core\Provider\DatabaseServiceProvider;
use Cops\Core\Provider\MobileDetectServiceProvider;
use Cops\Command\Provider\CommandServiceProvider;
use Cops\Core\Provider\UrlGeneratorServiceProvider;
use Cops\Core\Provider\TranslationServiceProvider;
use Cops\Security\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Cops\Back\Controller as Back;
use Cops\Front\Controller as Front;
use Cops\Core\ApplicationAwareInterface;
use Cops\Core\UseServiceKeyInterface;

/**
 * Main application
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Application extends BaseApplication
{
    /**
     * Internal DB key
     */
    const INTERNAL_DB_KEY = 'silexCops';

    /**
     * @inheritDoc
     */
    public function __construct(array $values = array(), array $overrideConfig = array())
    {
        parent::__construct($values);

        $app = $this;

        // Load & set configuration
        $this['config'] = $this->share(function ($c) use ($app, $overrideConfig) {
            return new \Cops\Core\Config($app['config-file'], $c['string-utils'], $overrideConfig);
        });

        $this['string-utils'] = $this->share(function ($c) {
            return new \Cops\Core\StringUtils;
        });

        if ($this['config']->getValue('debug')) {
            $app['debug'] = true;
        }

        $this->registerEntities();
        $this->registerFactories();
        $this->registerRepositories();
        $this->registerCollections();
        $this->registerModels();

        $this->registerServiceProviders();
        $this->registerRouting();

    }

    /**
     * Register the various app services
     *
     * @return void
     */
    private function registerServiceProviders()
    {
        $this
            ->register(new SecurityServiceProvider())
            ->register(new DatabaseServiceProvider())
            ->register(new CommandServiceProvider())
            ->register(new SessionServiceProvider)
            ->register(new UrlGeneratorServiceProvider)
            ->register(new TwigServiceProvider(), array(
                'twig.path' => array(
                    BASE_DIR . 'themes/' . $this['config']->getValue('theme'),
                    __DIR__ . '/../Templates',
                ),
                'twig.options' => array(
                    'cache' => realpath(BASE_DIR . 'cache'),
                )
            ))
            ->register(new FormServiceProvider())
            ->register(new ValidatorServiceProvider())
            // Translator
            ->register(new TranslationServiceProvider(
                array(
                    'default' => $this['config']->getValue('default_lang')
                )
            ));

        $this['translator'] = $this->share($this->extend('translator', function (Translator $translator) {
            $translator->addLoader('yaml', new YamlFileLoader());
            foreach (array('messages', 'admin', 'validators') as $domain) {
                $translator->addResource('yaml', BASE_DIR.'locales/fr/'.$domain.'.yml', 'fr', $domain);
                $translator->addResource('yaml', BASE_DIR.'locales/en/'.$domain.'.yml', 'en', $domain);
            }
            return $translator;
        }));

    }

    /**
     * Register routing
     *
     * @return void
     */
    private function registerRouting()
    {
        $app = $this;

        $this->get('/', function () use ($app) {
            // redirect to /default_db/default_lang/
            $redirect = $app['url_generator']->generate('homepage', array(
                '_locale'  => $app['config']->getValue('default_lang'),
                'database' => $app['config']->getValue('current_database_key'),
            ));
            return $app->redirect($redirect, 301);
        });

        // Admin related controllers
        $adminPath = $this['config']->getAdminPath();
        // Keep these up to avoid side effects on admin panel
        $this->mount($adminPath.'/{_locale}',                      new Back\IndexController($this));
        $this->mount($adminPath.'/{_locale}/{database}/database/', new Back\DatabaseController($this));
        $this->mount($adminPath.'/{_locale}/users/',               new Back\UserController($this));

        // Set the mount points for the controllers with database prefix
        $this->mount('{database}/{_locale}/',             new Front\IndexController($this));
        $this->mount('{database}/{_locale}/book/',        new Front\BookController($this));
        $this->mount('{database}/{_locale}/serie/',       new Front\SerieController($this));
        $this->mount('{database}/{_locale}/author/',      new Front\AuthorController($this));
        $this->mount('{database}/{_locale}/tag/',         new Front\TagController($this));
        $this->mount('{database}/{_locale}/search/',      new Front\SearchController($this));
        $this->mount('{database}/{_locale}/inline-edit/', new Front\InlineEditController($this));
        $this->mount('{database}/{_locale}/opds/',        new Front\OpdsController($this));
    }

    /**
     * Register entities in DIC
     *
     * @return void
     */
    private function registerEntities()
    {
        $this['entity.book'] = function ($c) {
            $book = new \Cops\Core\Entity\EditableBook(
                $c['cover'],
                $c['entity.serie'],
                $c['collection.author'],
                $c['collection.tag'],
                $c['collection.bookfile'],
                new \DateTime
            );
            return $book->setRepository($c['repository.book']);
        };

        $this['entity.author'] = function ($c) {
            $author = new \Cops\Core\Entity\Author($c['calibre-util']);
            return $author->setRepository($c['repository.author']);
        };

        $this['entity.serie'] = function ($c) {
            $serie = new \Cops\Core\Entity\Serie;
            return $serie->setRepository($c['repository.serie']);
        };

        $this['entity.tag'] = function ($c) {
            $tag = new \Cops\Core\Entity\Tag;
            return $tag->setRepository($c['repository.tag']);
        };

        $this['entity.user'] = function ($c) {
            $user = new \Cops\Core\Entity\User;
            return $user->setRepository($c['repository.user']);
        };
    }

    /**
     * Register models in DIC
     *
     * @return void
     */
    private function registerModels()
    {
        // Calibre util
        $this['calibre-util'] = $this->share(function ($c) {
            $calibre = new \Cops\Core\Calibre\Util($c['config']);
            return $calibre->setRepositoryClosure(function () use ($c) {
                return $c['repository.calibre-util'];
            });
        });

        $this['cover'] = $this->share(function ($c) {
             return new \Cops\Core\Cover($c['config'], $c['factory.image']);
        });

        // Form class
        $this['form.type.user'] = $this->share(function() {
            return new \Cops\Back\Form\UserType;
        });

        return $this;
    }

    /**
     * Register factories
     *
     * @return void
     */
    private function registerFactories()
    {
        $this['factory.bookfile'] = $this->share(function ($c) {
            $factory = new \Cops\Core\Entity\BookFile\BookFileFactory(array(
                'original_epub' => function () {
                    return new \Cops\Core\Entity\BookFile\Adapter\Epub;
                },
                'epub' => function () {
                    return new \Cops\Core\Entity\BookFile\Adapter\Epub;
                },
                'pdf' => function () {
                    return new \Cops\Core\Entity\BookFile\Adapter\Pdf;
                },
                'mobi' => function () {
                    return new \Cops\Core\Entity\BookFile\Adapter\Mobi;
                },
                'azw3' => function () {
                    return new \Cops\Core\Entity\BookFile\Adapter\Azw3;
                },
            ));

            return $factory->setTranslator($c['translator'])
                ->setStorageDir($c['config']->getDatabasePath());
        });

        $this['factory.image'] = $this->share(function ($c) {
            return new \Cops\Core\Image\ImageFactory(array(
                'gd' => function () use ($c) {
                    return new \Cops\Core\Image\Adapter\Gd($c['config']);
                },
                'targz' => function () use ($c) {
                    return new \Cops\Core\Image\Adapter\Imagick($c['config']);
                },
            ));
        });

        $this['factory.archive'] = $this->share(function ($c) {
            return new \Cops\Core\Archive\ArchiveFactory(array(
                'zip'   => function () use ($c) {
                    $zip = new \Cops\Core\Archive\Adapter\Zip($c['collection.bookfile']);
                    return $zip->setApp($c);
                },
                'targz' => function () use ($c) {
                    $targz = new \Cops\Core\Archive\Adapter\TarGz($c['collection.bookfile']);
                    return $targz->setApp($c);
                },
            ));
        });

        $this['factory.search'] = $this->share(function ($c) {
            return new \Cops\Core\Search\SearchFactory(array(
                'sqlite' => function () use ($c) {
                    return new \Cops\Core\Search\Adapter\Sqlite($c['collection.book']);
                },
            ));
        });
    }

    /**
     * Register repositories in DIC
     *
     * @return void
     */
    private function registerRepositories()
    {
        $this['repository.book'] = $this->share(function () {
            return new \Cops\Core\Entity\BookRepository;
        });

        $this['repository.author'] = $this->share(function ($c) {
            return new \Cops\Core\Entity\AuthorRepository($c['string-utils']);
        });

        $this['repository.serie'] = $this->share(function ($c) {
            return new \Cops\Core\Entity\SerieRepository($c['string-utils']);
        });

        $this['repository.tag'] = $this->share(function () {
            return new \Cops\Core\Entity\TagRepository;
        });

        $this['repository.bookfile'] = $this->share(function ($c) {
            return new \Cops\Core\Entity\BookFile\BookFileRepository;
        });

        $this['repository.user'] = $this->share(function ($c) {
            return new \Cops\Core\Entity\UserRepository;
        });

        $this['repository.calibre-util'] = $this->share(function () {
            return new \Cops\Core\Calibre\UtilRepository;
        });
    }

    /**
     * Register collections in DIC
     *
     * @return void
     */
    public function registerCollections()
    {
        $this['collection.book'] = function ($c) {
            $collection = new \Cops\Core\Entity\BookCollection;
            return $collection->setRepositoryClosure(function () use ($c) {
                return $c['repository.book'];
            });
        };

        $this['collection.author'] = function ($c) {
            $collection = new \Cops\Core\Entity\AuthorCollection;
            return $collection->setRepositoryClosure(function () use ($c) {
                return $c['repository.author'];
            });
        };

        $this['collection.serie'] = function ($c) {
            $collection = new \Cops\Core\Entity\SerieCollection;
            return $collection->setRepositoryClosure(function () use ($c) {
                return $c['repository.serie'];
            });
        };

        $this['collection.tag'] = function ($c) {
            $collection = new \Cops\Core\Entity\TagCollection;
            return $collection->setRepositoryClosure(function () use ($c) {
                return $c['repository.tag'];
            });
        };

        $this['collection.bookfile'] = function ($c) {
            $collection = new \Cops\Core\Entity\BookFile\BookFileCollection;
            return $collection->setRepositoryClosure(function () use ($c) {
                return $c['repository.bookfile'];
            });
        };

        $this['collection.user'] = function ($c) {
            $collection = new \Cops\Core\Entity\UserCollection;
            return $collection->setRepositoryClosure(function () use ($c) {
                return $c['repository.user'];
            });
        };
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($id)
    {
        $service = parent::offsetGet($id);

        if ($service instanceof UseServiceKeyInterface) {
            $service->setServiceKey($id);
        }

        if ($service instanceof ApplicationAwareInterface) {
            $service->setApp($this);
        }

        return $service;
    }
}
