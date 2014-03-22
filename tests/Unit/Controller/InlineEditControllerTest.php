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

    public function testUpdateAuthorKoWhenUser()
    {
        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_USER'));
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

        $this->assertFalse($this->client->getResponse()->isOk());
    }

    public function testUpdateBook()
    {
        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        // Update author
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

        // Set back correct author
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

        // Test with french locale
        $this->client->request(
            'POST',
            '/fr/inline-edit/3',
            array(
                'name'  => 'pubdate',
                'pk'    => 3,
                'value' => '01/12/1970', // d/m/Y
            )
        );
        $this->assertTrue($this->client->getResponse()->isOk());

        // Test with english locale
        $this->client->request(
            'POST',
            '/en/inline-edit/3',
            array(
                'name'  => 'pubdate',
                'pk'    => 3,
                'value' => '12/01/1970', // m/d/Y
            )
        );
        $this->assertTrue($this->client->getResponse()->isOk());

        // Update title
        $this->client->request(
            'POST',
            '/en/inline-edit/3',
            array(
                'name'  => 'title',
                'pk'    => 3,
                'value' => 'Les miséroïdes',
            )
        );
       $this->assertTrue($this->client->getResponse()->isOk());

        $this->client->request(
            'POST',
            '/en/inline-edit/3',
            array(
                'name'  => 'title',
                'pk'    => 3,
                'value' => 'Les misérables',
            )
        );
       $this->assertTrue($this->client->getResponse()->isOk());
    }
}