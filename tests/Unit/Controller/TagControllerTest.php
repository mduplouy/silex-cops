<?php

namespace Cops\Tests\Controller;

use Cops\Tests\AbstractTestCase;

class TagControllerTest extends AbstractTestCase
{
    public function testDetailPageOk()
    {
        $client = $this->createClient();

        $client->request('GET', '/test/fr/tag/3');
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/default/fr/tag/12313');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testDownloadOk()
    {
        $client = $this->createClient();

        $client->request('GET', '/test/fr/tag/3/download/zip');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

        $client->request('GET', '/default/fr/tag/3/download/tar.gz');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

    }

    public function testDownloadKo()
    {
        $client = $this->createClient();

        // Redirect to homepage
        $client->request('GET', '/test/fr/tag/13113112/download/zip');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to homepage
        $client->request('GET', '/default/fr/tag/3/download/dummy');
        $this->assertTrue($client->getResponse()->isOk());

    }
}