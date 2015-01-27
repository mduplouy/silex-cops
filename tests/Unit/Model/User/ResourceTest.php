<?php

namespace Cops\Tests\Model\User;

use Silex\WebTestCase;

class ResourceTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../../application.php';
    }

    public function testLoadAll()
    {
        $allUsers = $this->app['model.user']->getResource()->loadAll();

        $this->assertInternalType('array', $allUsers);
    }

    /**
     * @expectedException Cops\Exception\User\UserNotFoundException
     */
    public function testLoadNotExistingIdException()
    {
        $user = $this->app['model.user']->getResource()->load(123456);
    }
}
