<?php

namespace Cops\Tests\Back\Controller;

use Cops\Tests\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends AbstractTestCase
{
    /**
     * @return \Symfony\Component\HttpKernel\Client
     */
    protected function getAdminClient()
    {
        $client = $this->createClient();

        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('admin', 'test', $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }

    public function testNoAccessForNonAdmin()
    {
        // Create temporary database
        $this->app['repository.user']->createTable();

        $client = $this->createClient();

        $client->request('GET', '/admin/fr/users');
        $this->assertFalse($client->getResponse()->isOk());

        $session = $this->app['session'];

        $firewall = 'default';
        $token = new UsernamePasswordToken('test', 'test', $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $client->request('GET', '/admin/fr/users');
        $this->assertFalse($client->getResponse()->isOk());
    }

    public function testListAction()
    {
        $client = $this->getAdminClient();

        $client->request('GET', '/admin/fr/users');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('table id="js-user-list"', $client->getResponse()->getContent());
    }

    public function testAddAction()
    {
        $client = $this->getAdminClient();

        $client->request('GET', '/admin/fr/users/add');
        $this->assertTrue($client->getResponse()->isOk());

        $nbUsersBefore = $this->app['collection.user']->findAll()->count();

        $client->request('GET', '/admin/fr/users/add');

        $form = $client->getCrawler()->selectButton('user_save')->form();

        $values = array(
            'user[username]'                    => 'user-test',
            'user[password][password]'          => 'user-password',
            'user[password][password_confirm]'  => 'user-password',
            'user[role]'                        => 'ROLE_EDIT',
        );

        $crawler = $client->submit($form, $values);

        $this->assertTrue($client->getResponse()->isOk());

        $allUsers = $this->app['collection.user']->findAll();
        $nbUsers = $allUsers->count();

        $this->assertEquals($nbUsers, $nbUsersBefore+1);


        // Test another time to check it's well handled by form
        $client->request('GET', '/admin/fr/users/add');
        //$client->followRedirects(true);

        $form = $client->getCrawler()->selectButton('user_save')->form();

        $values = array(
            'user[username]'                    => 'user-test',
            'user[password][password]'          => 'user-password',
            'user[password][password_confirm]'  => 'user-password',
            'user[role]'                        => 'ROLE_EDIT',
        );

        $crawler = $client->submit($form, $values);

        $this->assertTrue($client->getResponse()->isOk());

        // Same numbers of users
        $this->assertEquals($this->app['collection.user']->findAll()->count(), $nbUsers);
    }

    /**
     * @depends testAddAction
     */
    public function testEditAction()
    {
        $lastUser = $this->app['entity.user']->findByUsername('user-test');

        $client = $this->getAdminClient();

        $client->request('GET', '/admin/fr/users/edit/'.$lastUser->getId());
        $this->assertTrue($client->getResponse()->isOk());


        $form = $client->getCrawler()->selectButton('user_save')->form();

        $values = array(
            'user[username]'                   => $lastUser->getUsername(),
            'user[password][password]'         => 'new-password',
            'user[password][password_confirm]' => 'new-password',
            'user[role]'                       => 'ROLE_ADMIN',
        );

        $crawler = $client->submit($form, $values);

        $this->assertTrue($client->getResponse()->isOk());
    }

    /**
     * @depends testEditAction
     */
    public function testDeleteAction()
    {
        $lastUser = $this->app['entity.user']->findByUsername('user-test');

        $client = $this->getAdminClient();

        $nbUsersBefore = $this->app['collection.user']->findAll()->count();

        $deleteUrl = '/admin/fr/users/delete/'.$lastUser->getId();

        $client->request('GET', $deleteUrl);
        $this->assertTrue($client->getResponse()->isOk());

        $allUsers = $this->app['collection.user']->findAll();
        $this->assertEquals($nbUsersBefore-1, $allUsers->count());

        $this->setExpectedException('Cops\Core\Entity\Exception\UserNotFoundException');
        $user = $this->app['entity.user']->findById($lastUser->getId());
    }

    /**
      * @depends testDeleteAction
     */
    public function testHandleFormException()
    {
        $client = $this->getAdminClient();
        $client->request('GET', '/admin/fr/users/add');

        $this->assertTrue($client->getResponse()->isOk());

        $form = $client->getCrawler()->selectButton('user_save')->form();

        // Delete temporary database
        unlink($this->app['config']->getValue('internal_db'));

        $values = array(
            'user[username]'                   => 'dummy-user',
            'user[password][password]'         => 'new-password',
            'user[password][password_confirm]' => 'new-password',
            'user[role]'                       => 'ROLE_ADMIN',
        );

        $crawler = $client->submit($form, $values);
        $this->assertFalse($client->getResponse()->isOk());
    }
}
