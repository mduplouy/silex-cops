<?php

namespace Cops\Tests\Controller;

use Cops\Tests\AbstractTestCase;

class AuthorControllerTest extends AbstractTestCase
{
    public function testDetailPageOk()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/test/fr/author/12313');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/default/fr/author/4');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/test/fr/author/list/A');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/default/fr/author/list/0');
        $this->assertTrue($client->getResponse()->isOk());

    }

    public function testDownloadOk()
    {
        $client = $this->createClient();
        $client->request('GET', '/default/fr/author/4/download/zip');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

        $client->request('GET', '/default/fr/author/4/download/tar.gz');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

    }

    public function testDownloadKo()
    {
        $client = $this->createClient();

        // Redirect to homepage
        $client->request('GET', '/default/fr/author/13113112/download/zip');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to homepage
        $client->request('GET', '/default/fr/author/1/download/zip');
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/default/fr/author/4/download/dummy');
        $this->assertTrue($client->getResponse()->isOk());
    }
}