<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * User
 *
 * @require PHP 5.3
 */
class UserTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Entity\User', $this->getUser());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testFindByUsernameException()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findByUsername')
            ->will($this->returnValue(array()));

        $this->getUser()->setRepository($mock)->findByUsername('dummy');
    }

    public function testFindByUserame()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findByUsername')
            ->will($this->returnValue(array(
                'id'       => '1',
                'username' => 'dummy',
                'password' => 'dummy',
                'roles'    => 'dummy',
            )));

        $user = $this->getUser()->setRepository($mock)->findByUsername('dummy');

        $this->assertEquals($user->getId(),    1);
        $this->assertEquals($user->getRoles(), array('dummy'));
    }

    /**
     * @expectedException \Cops\Core\Entity\Exception\UserNotFoundException
     */
    public function testFindByIdException()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue(array()));

        $this->getUser()->setRepository($mock)->findById(1);
    }

    public function testFindById()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue(array(
                'id'       => '1',
                'username' => 'dummy',
                'password' => 'dummy',
                'roles'    => 'dummy',
            )));

        $user = $this->getUser()->setRepository($mock)->findById(1);

        $this->assertEquals($user->getUsername(), 'dummy');
        $this->assertEquals($user->getRoles(), array('dummy'));
    }

    /**
     * Get user
     * @return \Cops\Core\Èntity\User
     */
    protected function getUser()
    {
        return $this->app['entity.user'];
    }

    /**
     * Get mock repository
     */
    protected function getMockRepository()
    {
        return $this->getMockBuilder('\Cops\Core\Entity\UserRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
