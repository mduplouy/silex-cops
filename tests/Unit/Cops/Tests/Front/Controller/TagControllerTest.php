<?php

namespace Cops\Tests\Front\Controller;

use Cops\Tests\AbstractTestCase;

class TagControllerTest extends AbstractTestCase
{
    public function testDetailAction()
    {
        $client = $this->createClient();

        $client->request('GET', '/test/fr/tag/3');
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/test/fr/tag/12313');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testDownloadAction()
    {
        $client = $this->createClient();

        $client->request('GET', '/test/fr/tag/3/download/zip');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

        $client->request('GET', '/test/fr/tag/3/download/targz');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

        // Redirect to homepage
        $client->request('GET', '/test/fr/tag/13113112/download/zip');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to homepage
        $client->request('GET', '/test/fr/tag/3/download/dummy');
        $this->assertTrue($client->getResponse()->isOk());
    }
}
