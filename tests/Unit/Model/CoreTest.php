<?php

namespace Cops\Tests\Model;

use Cops\Model\Core;
use Silex\WebTestCase;

/**
 * Core model test cases
 *
 * @require PHP 5.3
 */
class CoreTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new \Silex\Application();

        // Define core model, no closure to ensure loading
        // Load configuration & set service providers
        $app['config'] = new \Cops\Model\Config(BASE_DIR.'app/cops/config.ini', new \Cops\Model\Utils);
        $app['core']   = new \Cops\Model\Core($app);
        $app['debug']  = true;

        // Register special database for tests
        $app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_sqlite',
                'path'     => DATABASE,
            ),
        ));
        return $app;
    }

    /**
     * @dataProvider getModels
     */
    public function testGetModel($modelName, $className)
    {
        $model = $this->app[$modelName];
        $this->assertInstanceOf($className, $model);
    }

    public function getModels()
    {
        return array(
            array('utils',          '\Cops\Model\Utils'),
            array('model.book',     '\Cops\Model\Book'),
            array('model.author',   '\Cops\Model\Author'),
            array('model.serie',    '\Cops\Model\Serie'),
            array('model.tag',      '\Cops\Model\Tag'),
            array('model.cover',    '\Cops\Model\Cover'),
            array('model.bookfile', '\Cops\Model\BookFile'),
            array('model.calibre',  '\Cops\Model\Calibre'),

            array('factory.bookfile', '\Cops\Model\BookFile\BookFileFactory'),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetModelException()
    {
        $this->app['model.dummy'];
    }

    public function testConfigInstance()
    {
        $this->assertInstanceOf('Cops\Model\Config', $this->app['config']);
    }

    public function testTranslation()
    {
        $series = $this->app['translator']->trans('Series', array(), null, 'fr_FR');
        $this->assertEquals('Séries', $series);
    }
}