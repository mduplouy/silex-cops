<?php

namespace Cops\Tests\Security\User;

use Cops\Tests\AbstractTestCase;

class ProviderTest extends AbstractTestCase
{
    public function testLoadUserByUsername()
    {
        $mock = $this->getMockBuilder('\Cops\Core\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('findByUsername');

        $provider = new \Cops\Security\User\Provider($mock);

        $provider->loadUserByUsername('any');
    }

    public function testRefreshUser()
    {
        $mock = $this->getMockBuilder('\Cops\Core\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('findByUsername');

        $mock->expects($this->once())
            ->method('getUsername');

        $provider = new \Cops\Security\User\Provider($mock);

        $provider->refreshUser($mock);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshUserException()
    {
        $mock = $this->getMockBuilder('\Cops\Core\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $wrongUser = $this->getMockBuilder('\Symfony\Component\Security\Core\User\UserInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $provider = new \Cops\Security\User\Provider($mock);

        $provider->refreshUser($wrongUser);
    }

    /**
     * @dataProvider classProvider
     */
    public function testSupportsClass($class, $expectedResult)
    {
        $user = $this->app['entity.user'];

        $provider = new \Cops\Security\User\Provider($user);

        $this->assertEquals($provider->supportsClass($class), $expectedResult);
    }

    public function classProvider()
    {
        return array(
            array('Cops\Core\Entity\User', true),
            array('Cops\Core\Entity\Book', false),
            array('Symfony\Component\Security\Core\User\UserInterface', false),
        );
    }
}
