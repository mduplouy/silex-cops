<?php

namespace Cops\Tests\Front\Controller;

use Cops\Tests\AbstractTestCase;

class SearchControllerTest extends AbstractTestCase
{
    public function testDetailAction()
    {
        $client = $this->createClient();

        $client->request('POST', '/test/fr/search/', array('keywords' => 'hugo'));
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/test/fr/search/hugo/results');
        $this->assertTrue($client->getResponse()->isOk());
    }
}
