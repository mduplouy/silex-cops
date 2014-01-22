<?php

namespace Cops\Tests\Controller;

use Silex\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    public function testHomepageResponseCode()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/fr/search/', array('keywords' => 'author'));
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/fr/search/author/results');
        $this->assertTrue($client->getResponse()->isOk());
    }

}
