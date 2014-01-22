<?php

namespace Cops\Tests\Controller;

use Silex\WebTestCase;

class IndexControllerTest extends WebTestCase
{

    private $crawler;
    private $response;

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

    private function requestHome()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/fr/');

        $this->crawler  = $crawler;
        $this->response = $client->getResponse();
    }


    public function testHomepageResponseCode()
    {
        $this->requestHome();
        $this->assertTrue($this->response->isOk());
    }

}
