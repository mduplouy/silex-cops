<?php

namespace Cops\Tests\Back\Controller;

use Cops\Tests\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DatabaseControllerTest extends AbstractTestCase
{
    public function testNoAccessForNonAdmin()
    {
        $client = $this->createClient();

        $client->request('GET', '/admin/fr/test/database/triggers');
        $this->assertFalse($client->getResponse()->isOk());

        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $client->request('GET', '/admin/fr/test/database/triggers');
        $this->assertFalse($client->getResponse()->isOk());
    }

    public function testAccessAndUpdatesForAdmin()
    {
        $client = $this->createClient();

        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('admin', 'test', $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $client->request('GET', '/admin/fr/test/database/triggers');
        $this->assertTrue($client->getResponse()->isOk());

        // Remove DB triggers
        $client->request(
            'POST',
            '/admin/fr/test/database/triggers',
            array(
                'triggers' => array(
                    'books_insert_trg' => 0,
                    'books_update_trg' => 0,
                ),
            )
        );

        $this->assertTrue($client->getResponse()->isOk());

        // Restore DB triggers
        $client->request(
            'POST',
            '/admin/fr/test/database/triggers',
            array(
                'triggers' => array(
                    'books_insert_trg' => 1,
                    'books_update_trg' => 1,
                ),
            )
        );
        $this->assertTrue($client->getResponse()->isOk());
    }
}
