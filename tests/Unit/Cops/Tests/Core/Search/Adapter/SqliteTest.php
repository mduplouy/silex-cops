<?php

namespace Cops\Tests\Core\Search\Adapter;

use Cops\Tests\AbstractTestCase;
use Cops\Core\Search\Adapter\Sqlite;

class SqliteTest extends AbstractTestCase
{
    public function testService()
    {
        $factory = $this->app['factory.search'];

        $this->assertInstanceOf('\Cops\Core\Search\Adapter\Sqlite', $factory->getInstance('sqlite'));
    }

    /**
     * @expectedException \Cops\Core\Entity\Exception\BookNotFoundException
     */
    public function testGetResultsException()
    {
        $bookCollectionMock = $this->getMockBuilder('\Cops\Core\Entity\BookCollection')
            ->setMethods(array('setFirstResult', 'setMaxResults', 'findByKeyword', 'count'))
            ->disableOriginalConstructor()
            ->getMock();

        $bookCollectionMock->expects($this->once())
            ->method('setFirstResult')
            ->will($this->returnSelf());

        $bookCollectionMock->expects($this->once())
            ->method('setMaxResults')
            ->will($this->returnSelf());

        $bookCollectionMock->expects($this->once())
            ->method('findByKeyword')
            ->will($this->returnSelf());

        $bookCollectionMock->expects($this->once())
            ->method('count')
            ->will($this->returnValue(0));

        $sqliteAdapter = new Sqlite($bookCollectionMock, $this->app['string-utils']);

        $sqliteAdapter->getResults(array('one', 'term'));
    }
}