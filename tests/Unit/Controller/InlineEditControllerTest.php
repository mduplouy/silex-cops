<?php

namespace Cops\Tests\Controller;

use Silex\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class InlineEditControllerTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    public function setUp()
    {
        parent::setUp();
        $this->client = $this->createClient();
    }

    public function testUpdateAuthorKoWhenNotLoggedIn()
    {
        $this->client->request(
            'POST',
            '/fr/inline-edit/3',
            array(
                'name'  => 'author',
                'pk'    => 3,
                'value' => 'John Smith',
            )
        );

        $this->assertFalse($this->client->getResponse()->isOk());
    }

    public function testUpdateAuthor()
    {
        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request(
            'POST',
            '/fr/inline-edit/3',
            array(
                'name'  => 'author',
                'pk'    => 3,
                'value' => 'John Smith',
            )
        );

        $this->assertTrue($this->client->getResponse()->isOk());

        $this->client->request(
            'POST',
            '/fr/inline-edit/3',
            array(
                'name'  => 'author',
                'pk'    => 3,
                'value' => 'Victor Hugo',
            )
        );

        $this->assertTrue($this->client->getResponse()->isOk());
    }
}