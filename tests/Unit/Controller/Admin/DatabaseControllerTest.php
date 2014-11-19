<?php

namespace Cops\Tests\Controller\Admin;

use Silex\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DatabaseControllerTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../../application.php';
    }

    public function setUp()
    {
        parent::setUp();
        $this->client = $this->createClient();
    }

    public function testNoAccessForNonAdmin()
    {
        $this->client->request('GET', '/admin/fr/database/triggers');
        $this->assertFalse($this->client->getResponse()->isOk());

        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET', '/admin/fr/database/triggers');
        $this->assertFalse($this->client->getResponse()->isOk());
    }

    public function testAccessAndUpdatesForAdmin()
    {
        $session = $this->app['session'];

        $firewall = 'admin';
        $token = new UsernamePasswordToken('admin', 'test', $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET', '/admin/fr/database/triggers');
        $this->assertTrue($this->client->getResponse()->isOk());

        // Remove DB triggers
        $this->client->request(
            'POST',
            '/admin/fr/database/triggers',
            array(
                'triggers' => array(
                    'books_insert_trg' => 0,
                    'books_update_trg' => 0,
                ),
            )
        );
        $this->assertTrue($this->client->getResponse()->isOk());

        // Restore DB triggers
        $this->client->request(
            'POST',
            '/admin/fr/database/triggers',
            array(
                'triggers' => array(
                    'books_insert_trg' => 1,
                    'books_update_trg' => 1,
                ),
            )
        );
        $this->assertTrue($this->client->getResponse()->isOk());
    }

}