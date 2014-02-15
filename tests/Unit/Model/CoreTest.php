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
        $app = new \Cops\Model\Application();

        // Define core model, no closure to ensure loading
        // Load configuration & set service providers
        $app['core'] =  new \Cops\Model\Core(BASE_DIR.'app/cops/config.ini', $app);

        $app['debug'] = true;

        // Register special database for tests
        $app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_sqlite',
                'path'     => DATABASE,
            ),
        ));
        return $app;
    }

    public function testGetModel()
    {
        $tag = $this->app['core']->getModel('Tag');
        $this->assertInstanceOf('Cops\Model\Tag', $tag);
        $tag = $this->app['core']->getModel('Tag');
        $this->assertInstanceOf('Cops\Model\Common', $tag);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetModelException()
    {
        $this->app['core']->getModel('dummy');
    }

    public function testConfigInstance()
    {
        $this->assertInstanceOf('Cops\Model\Config', Core::getConfig());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCoreCollectionException()
    {
        $this->app['core']->getCollection();
    }

    public function testTranslation()
    {
        $series = $this->app['translator']->trans('Series', array(), null, 'fr_FR');
        $this->assertEquals('Séries', $series);
    }
}
