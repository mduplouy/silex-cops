<?php

namespace Cops\Tests\Front\Controller;

use Cops\Tests\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserBooksControllerTest extends AbstractTestCase
{
    protected function getAdminUserClient()
    {
        $client = $this->createClient();
        $client->getCookieJar()->clear();

        $user = $this->app['entity.user'];
        $user->setId(1)
            ->setUsername('admin')
            ->setPassword('password')
            ->setRole('ROLE_USER_ACTIONS');

        $firewall = 'default';
        $token = new UsernamePasswordToken($user, 'test', $firewall, array('ROLE_USER_ACTIONS'));

        $session = $this->app['session'];
        $session->clear();
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }

    public function testCheckListEmptyThenAddBookThenListIsFilled()
    {
        $client = $this->getAdminUserClient();

        foreach ($this->app['entity.user-book']->getAvailableActions() as $action => $label) {

            $client->request('GET', '/test/fr/user-books/'.$action);
            $this->assertTrue($client->getResponse()->isOk());

            $this->assertContains('Cette liste est vide', $client->getResponse()->getContent());

            $client->request(
                'POST',
                '/test/fr/user-books/action/5',
                array(
                    'action' => $action,
                    'status' => 'true',
                )
            );
            $this->assertTrue($client->getResponse()->isOk());

            $client->request('GET', '/test/fr/user-books/'.$action);
            $this->assertTrue($client->getResponse()->isOk());

            $this->assertNotContains('Cette liste est vide', $client->getResponse()->getContent());

            $client->request(
                'POST',
                '/test/fr/user-books/action/5',
                array(
                    'action' => $action,
                    'status' => 'false',
                )
            );
            $this->assertTrue($client->getResponse()->isOk());
        }
    }

    public function testAccessForbiddenForRole()
    {
        $client = $this->createClient();
        $client->getCookieJar()->clear();

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_USER'));

        $session = $this->app['session'];
        $session->clear();
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        foreach ($this->app['entity.user-book']->getAvailableActions() as $action => $label) {

            $client->request('GET', '/test/fr/user-books/'.$action);
            $this->assertEquals($client->getResponse()->getStatusCode(), 403);

            $client->request(
                'POST',
                '/test/fr/user-books/action/5',
                array(
                    'action' => $action,
                    'status' => 'true',
                )
            );
            $this->assertEquals($client->getResponse()->getStatusCode(), 403);
        }

    }

    public function testSelectionAction()
    {
        $client = $this->getAdminUserClient();

        foreach ($this->app['entity.user-book']->getAvailableActions() as $action => $label) {

            // Add the book
            $crawler = $client->request(
                'POST',
                '/test/fr/user-books/action/5',
                array(
                    'action' => $action,
                    'status' => 'true',
                )
            );
            $this->assertTrue($client->getResponse()->isOk());

            // Send an empty selection
            $client->request(
                'POST',
                '/test/fr/user-books/'.$action,
                array(
                    'download' => 'zip',
                    'book_id' => array(),
                )
            );
            $this->assertTrue($client->getResponse()->isOk());

            // send non existant books
            $client->request(
                'POST',
                '/test/fr/user-books/'.$action,
                array(
                    'download' => 'zip',
                    'book_id' => array(10, 11, 12),
                )
            );
            $this->assertTrue($client->getResponse()->isOk());

            $client->request(
                'POST',
                '/test/fr/user-books/'.$action,
                array(
                    'download' => 'zip',
                    'book_id' => array(1, 5),
                )
            );
            $this->assertTrue($client->getResponse()->isOk());

            $client->request(
                'POST',
                '/test/fr/user-books/'.$action,
                array(
                    'download' => 'targz',
                    'book_id' => array(1, 5),
                )
            );
            $this->assertTrue($client->getResponse()->isOk());

            $client->request(
                'POST',
                '/test/fr/user-books/'.$action,
                array(
                    'remove' => 'remove',
                    'book_id' =>  array(1, 5),
                )
            );

            $this->assertTrue($client->getResponse()->isOk());

        }
    }
}
