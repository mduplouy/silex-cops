<?php

namespace Cops\Tests\Controller;

use Cops\Tests\AbstractTestCase;

class SerieControllerTest extends AbstractTestCase
{
    public function testDetailPageOk()
    {
        $client = $this->createClient();

        $client->request('GET', '/test/fr/serie/1');
        $this->assertTrue($client->getResponse()->isOk());

        $client->followRedirects(true);
        $client->request('GET', '/default/fr/serie/12313');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/test/fr/serie/list/A');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/default/fr/serie/list/0');
        $this->assertTrue($client->getResponse()->isOk());
    }


    public function testDownloadOk()
    {
        $client = $this->createClient();
        $client->followRedirects(false);

        $crawler = $client->request('GET', '/test/fr/serie/1/download/zip');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

        $crawler = $client->request('GET', '/default/fr/serie/1/download/tar.gz');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

    }

    public function testDownloadKo()
    {
        $client = $this->createClient();
        $client->followRedirects(true);

        // Redirect to homepage
        $client->request('GET', '/test/fr/serie/13113112/download/zip');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to homepage
        $client->request('GET', '/default/fr/serie/1/download/dummy');
        $this->assertTrue($client->getResponse()->isOk());

    }
}