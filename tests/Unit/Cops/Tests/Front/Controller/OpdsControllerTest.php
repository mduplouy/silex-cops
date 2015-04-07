<?php

namespace Cops\Tests\Front\Controller;

use Cops\Tests\AbstractTestCase;

class OpdsControllerTest extends AbstractTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testUrl($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function urlProvider()
    {
        return array(
            array('/test/fr/opds'),
            array('/test/fr/opds/authors'),
            array('/test/fr/opds/authors/H'),
            array('/test/fr/opds/author/4'),
            array('/test/fr/opds/series'),
            array('/test/fr/opds/series/H'),
            array('/test/fr/opds/serie/1'),
        );
    }
}
