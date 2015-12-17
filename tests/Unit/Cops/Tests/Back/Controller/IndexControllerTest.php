<?php

namespace Cops\Tests\Back\Controller;

use Cops\Tests\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class IndexControllerTest extends AbstractTestCase
{
    public function testNoAccessForNonAdmin()
    {
        // Try with default USER role
        $client = $this->createClient();
        $client->request('GET', '/admin/fr/');
        $this->assertFalse($client->getResponse()->isOk());

        // Empty cookies and check there is no access
        $client->getCookieJar()->clear();
        $client->request('GET', '/admin/fr/');
        $this->assertFalse($client->getResponse()->isOk());
    }

    public function testAccessForAdmin()
    {
        $client = $this->createClient();

        // Override current USER role with admin one
        $session = $this->app['session'];
        $firewall = 'default';
        $token = new UsernamePasswordToken('admin', 'test', $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $client->request('GET', '/admin/fr/');
        $this->assertTrue($client->getResponse()->isOk());
    }
}
