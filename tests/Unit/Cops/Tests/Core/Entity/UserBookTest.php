<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * User Book
 *
 */
class UserBookTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Entity\UserBook', $this->getUserBook());
    }

    public function testSave()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(true));

        $this->getUserBook()->setRepository($mock)->save();
    }

    public function testDelete()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));

        $this->getUserBook()->setRepository($mock)->delete();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetActionException()
    {
        $this->getUserBook()->setAction('dummy');
    }

    /**
     * @dataProvider getActions
     */
    public function testSetAction($action)
    {
        $this->getUserBook()->setAction($action);
    }

    public function getActions()
    {
        return array(
            array(\Cops\Core\Entity\UserBook::ACTION_DOWNLOAD),
            array(\Cops\Core\Entity\UserBook::ACTION_TOREAD),
            array(\Cops\Core\Entity\UserBook::ACTION_FAVORITE),
        );
    }

    /**
     * Get user book
     * @return \Cops\Core\Èntity\UserBook
     */
    protected function getUserBook()
    {
        return $this->app['entity.user-book'];
    }

    /**
     * Get mock repository
     */
    protected function getMockRepository()
    {
        return $this->getMockBuilder('\Cops\Core\Entity\UserBookRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
