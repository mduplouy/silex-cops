<?php

namespace Cops\Tests\Controller;

use Silex\WebTestCase;

class BookControllerTest extends WebTestCase
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

    public function testBookDetailPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/fr/book/3');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testBookDetailPageKo()
    {
        // Route not found
        $client = $this->createClient();
        $crawler = $client->request('GET', '/fr/book/eaeaz');
        $this->assertTrue($client->getResponse()->isNotFound());

        // Redirect to homepage
        $crawler = $client->request('GET', '/fr/book/134561233132213');
        $this->assertTrue($client->getResponse()->isOk(), 'Non-existant book doest not redirect to homepage');

        // File not found => 404
        $crawler = $client->request('GET', '/fr/book/download/3/PDF');
        $this->assertEquals($client->getResponse()->getStatusCode(), 404, "Non-existant book file does not send a 404");

        // Redirect to homepage
        $crawler = $client->request('GET', '/fr/book/download/134561233132213/EPUB');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to book detail page
        $crawler = $client->request('GET', '/fr/book/download/134561233132213/DUMMY');
        $this->assertTrue($client->getResponse()->isOk());
    }
}
