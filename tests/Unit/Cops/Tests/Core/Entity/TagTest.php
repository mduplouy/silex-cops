<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * Tag
 *
 * @require PHP 5.3
 */
class TagTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Entity\Tag', $this->getTag());
    }

    /**
     * @expectedException \Cops\Core\Entity\Exception\TagNotFoundException
     */
    public function testFindByIdException()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue(array()));

        $this->getTag()->setRepository($mock)->findById(1);
    }

    public function testFindById()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue(array(
                'name'       => 'dummy',
                'book_count' => '10',
            )));

        $tag = $this->getTag()->setRepository($mock)->findById(1);

        $this->assertEquals($tag->getName(), 'dummy');
        $this->assertEquals($tag->getBookCount(), 10);
    }

    /**
     * @expectedException \Cops\Core\Entity\Exception\TagNotFoundException
     */
    public function testFindByNameException()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findByName')
            ->will($this->returnValue(array()));

        $this->getTag()->setRepository($mock)->findByName('dummy');
    }

    public function testFindByName()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findByName')
            ->will($this->returnValue(array(
                'id'         => '1',
                'book_count' => '10',
            )));

        $tag = $this->getTag()->setRepository($mock)->findByName('dummy');

        $this->assertEquals($tag->getId(), 1);
        $this->assertEquals($tag->getBookCount(), 10);
    }

    public function testGetBookCount()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('countBooks')
            ->will($this->returnValue(1));

        $tag = $this->getTag()->setRepository($mock)->setId(1);

        $tag->getBookCount();
        // No further call to repository
        $tag->getBookCount();
    }

    public function testDeleteFromBook()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('deleteFromBook');

        $tag = $this->getTag()->setRepository($mock)->setId(1);

        $bookMock = $this->getMockBuilder('\Cops\Core\Entity\Book')
            ->disableOriginalConstructor()
            ->getMock();

        $tag->deleteFromBook($bookMock);
    }

    public function testAssociateToBook()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('associateToBook');

        $tag = $this->getTag()->setRepository($mock)->setId(1);

        $bookMock = $this->getMockBuilder('\Cops\Core\Entity\Book')
            ->disableOriginalConstructor()
            ->getMock();

        $tag->associateToBook($bookMock);
    }

    /**
     * Get tag
     * @return \Cops\Core\Èntity\Tag
     */
    protected function getTag()
    {
        return $this->app['entity.tag'];
    }

    /**
     * Get mock repository
     */
    protected function getMockRepository()
    {
        return $this->getMockBuilder('\Cops\Core\Entity\TagRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
