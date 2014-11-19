<?php

namespace Cops\Tests\Controller;

use Cops\Tests\AbstractTestCase;

class IndexControllerTest extends AbstractTestCase
{
    public function testHomepageResponseCode()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/default/fr/');
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/test/fr/');
        $this->assertTrue($client->getResponse()->isOk());
    }

}
