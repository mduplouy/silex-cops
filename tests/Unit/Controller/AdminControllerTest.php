<?php

namespace Cops\Tests\Controller;

use Silex\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminControllerTest extends WebTestCase
{
    protected $client;

    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    public function testNoAccessForNonAdmin()
    {
        $this->client = $this->createClient();
        $this->client->request('GET', '/fr/admin/');
        $this->assertFalse($this->client->getResponse()->isOk());

        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('admin', 'test', $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET', '/fr/admin');
        $this->assertFalse($this->client->getResponse()->isOk());
    }

    public function testAccessForAdmin()
    {
        $this->client = $this->createClient();
        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('admin', 'test', $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET', '/fr/admin');
        $this->assertTrue($this->client->getResponse()->isOk());
    }
}