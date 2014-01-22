<?php

namespace Cops\Tests\Controller;

use Silex\WebTestCase;

class IndexControllerTest extends WebTestCase
{

    private $crawler;
    private $response;

    public function createApplication()
    {
        return require __DIR__.'/../application.php';
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
