<?php

namespace Cops\Tests\Front\Controller;

use Cops\Tests\AbstractTestCase;

class BookControllerTest extends AbstractTestCase
{
    public function testDetailAction()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/test/fr/book/5');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testListByDateAction()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/test/fr/book/by-date');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testDetailActionKo()
    {
        // Route not found
        $client = $this->createClient();
        $crawler = $client->request('GET', '/test/fr/book/eaeaz');
        $this->assertTrue($client->getResponse()->isNotFound());

        // Redirect to homepage
        $crawler = $client->request('GET', '/test/fr/book/134561233132213');
        $this->assertTrue($client->getResponse()->isOk(), 'Non-existant book doest not redirect to homepage');

        // File not found => redirect to book page
        $crawler = $client->request('GET', '/test/fr/book/download/3/PDF');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to homepage
        $crawler = $client->request('GET', '/test/fr/book/download/134561233132213/EPUB');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to homepage
        $crawler = $client->request('GET', '/test/fr/book/download/134561233132213/DUMMY');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testDownloadAction()
    {
        $client = $this->createClient();

        $client->request('GET', '/test/fr/book/download/5/MOBI');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/test/fr/book/download/5/PDF');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/test/fr/book/download/5/EPUB');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to book page
        $client->request('GET', '/test/fr/book/download/3/DUMMY');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to book page
        $client->request('GET', '/test/fr/book/download/3/MOBI');
        $this->assertTrue($client->getResponse()->isOk());
    }
}
