<?php

namespace Cops\Tests\Controller;

use Cops\Tests\AbstractTestCase;

class SearchControllerTest extends AbstractTestCase
{
    public function testSearchResponseCode()
    {
        $client = $this->createClient();
        $client->request('POST', '/default/fr/search/', array('keywords' => 'author'));
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/test/fr/search/author/results');
        $this->assertTrue($client->getResponse()->isOk());
    }

}
