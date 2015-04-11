<?php

namespace Cops\Tests\Front\Controller;

use Cops\Tests\AbstractTestCase;

class SearchControllerTest extends AbstractTestCase
{
    public function testSearchAction()
    {
        $client = $this->createClient();

        $client->request('POST', '/test/fr/search/', array('keywords' => 'hugo'));
        $this->assertTrue($client->getResponse()->isOk());

        // Update title
        $client->request(
            'POST',
            '/default/fr/inline-edit/3',
            array(
                'name'  => 'title',
                'pk'    => 3,
                'value' => 'la monarchie',
            )
        );

        $client->request('GET', '/test/fr/search/monarchie/results');
        $this->assertTrue($client->getResponse()->isOk());

        // Put back default value
        $client->request(
            'POST',
            '/default/fr/inline-edit/3',
            array(
                'name'  => 'title',
                'pk'    => 3,
                'value' => 'Les Misérables',
            )
        );

    }

    public function testNoResultAction()
    {
        $client = $this->createClient();

        $client->request('POST', '/test/fr/search/', array('keywords' => 'anything'));

        $crawler = $client->getCrawler();

        $noResultDiv = $crawler->filterXPath("//div[@id='search_results']/div");

        // No children in this div
        $this->assertEquals($noResultDiv->children()->count(), 0);
    }
}
