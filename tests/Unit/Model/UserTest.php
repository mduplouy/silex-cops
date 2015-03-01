<?php

namespace Cops\Tests\Model;

use Silex\WebTestCase;

class UserTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    public function testUser()
    {
        /**
         * @var \Cops\Model\User
         */
        $user = $this->app['model.user'];

        $this->assertInstanceOf('\Cops\Model\User\Resource', $user->getResource());

        $userData = array(
            'username' => 'test-user',
            'role'     => 'ROLE_USER',
            'roles'    => 'ROLE_USER',
            'password' => '123456',
        );

        $user->setData($userData);

        $this->assertEquals($user->getUsername(), 'test-user');
        $this->assertEquals($user->getRole(), 'ROLE_USER');
        $this->assertEquals($user->getRolesAsString(), 'ROLE_USER');
        $this->assertInternalType('array', $user->getRoles());
        $this->assertEquals($user->getPassword(), '123456');
        $this->assertNull($user->getSalt());

        $this->assertEquals($user->getAdminRole(), \Cops\Model\User::ROLE_ADMIN);
        $this->assertEquals($user->getUserRole(),  \Cops\Model\User::ROLE_USER);
        $this->assertEquals($user->getEditRole(),  \Cops\Model\User::ROLE_EDIT);
        $this->assertInternalType('array', $user->getAllRoles());

        // Save user
        $userId = $user->save();
        $this->assertEquals($userId, $user->getId());

        $serialize = serialize(array($user->getId(), 'test-user', '123456', array('ROLE_USER')));
        $this->assertEquals($user->serialize(), $serialize);
        $this->assertInstanceOf('\Cops\Model\User', $user->unserialize($serialize));

        // Update user
        $user->setRole('ROLE_ADMIN')
            ->save();

        $user->eraseCredentials();
        $this->assertNull($user->getPassword());

        // Delete
        $user->delete();

    }

    public function testLoad()
    {
        $user = $this->app['model.user']->loadByUsername('admin');
        $this->assertInstanceOf('\Cops\Model\User', $user);
        $this->assertEquals($user->getRole(), 'ROLE_ADMIN');

        $otherUser = $this->app['model.user']->load($user->getId());

        $this->assertEquals($user, $otherUser);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadNotExistingUsernameException()
    {
        $user = $this->app['model.user']->loadByUsername('wont-exist');
    }
}