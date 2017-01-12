<?php

namespace Cops\Tests\Front\Controller;

use Cops\Tests\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SearchControllerTest extends AbstractTestCase
{
    public function testSingleResultAction()
    {
        $client = $this->createClient();

        $client->request('POST', '/test/fr/search/', array('keywords' => 'hugo'));
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/test/fr/search/hugo/results');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testSeveralResultsAction()
    {
        $client = $this->createClient();

        $client->getCookieJar()->clear();

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_EDIT'));

        $session = $this->app['session'];
        $session->clear();
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $book = $this->app['entity.book']->findById(5);

        // Update book title so search can return more than one book
        $client->request(
            'POST',
            '/test/fr/inline-edit/5',
            array(
                'name'  => 'serie-name',
                'pk'    => 5,
                'value' => 'les miserables',
            )
        );

        $client->request('POST', '/test/fr/search/', array('keywords' => 'les miserables'));
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/test/fr/search/les-miserables/results');
        $this->assertTrue($client->getResponse()->isOk());

        // Revert book title to its original value
        $client->request(
            'POST',
            '/test/fr/inline-edit/5',
            array(
                'name'  => 'serie-name',
                'pk'    => 5,
                'value' => $book->getSerie()->getName(),
            )
        );
    }

    public function testNoResultAction()
    {
        $client = $this->createClient();

        $client->request('POST', '/test/fr/search/', array('keywords' => 'youwontfindthis'));
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/test/fr/search/youwontfindthis/results');
        $this->assertTrue($client->getResponse()->isOk());
    }
}
