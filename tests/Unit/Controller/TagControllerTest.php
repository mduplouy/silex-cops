<?php

namespace Cops\Tests\Controller;

use Silex\WebTestCase;

class TagControllerTest extends WebTestCase
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
                'path'     => __DIR__ . '/../database.db',
            ),
        ));
        return $app;
    }

    public function testDetailPageOk()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/fr/tag/2');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/fr/tag/12313');
        $this->assertTrue($client->getResponse()->isOk());
    }
}