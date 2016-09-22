<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * User Book repository
 *
 */
class UserBookRepositoryTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $instance = $this->getUserBookRepository();

        $this->assertInstanceOf('\Cops\Core\Entity\UserBookRepository', $instance);
        $this->assertInstanceOf('\Cops\Core\Entity\RepositoryInterface\UserBookRepositoryInterface', $instance);
        $this->assertInstanceOf('\Cops\Core\\InternalRepositoryInterface', $instance);
    }

    public function testGetTableStructure()
    {
        $this->assertInstanceOf('\Doctrine\DBAL\Schema\Table', $this->getUserBookRepository()->getTableStructure());
    }

    public function testModifyRow()
    {
        $connectionMock = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->setMethods(array('insert', 'delete'))
            ->getMock();

        $connectionMock->expects($this->once())
            ->method('insert')
            ->will($this->returnSelf());

        $connectionMock->expects($this->once())
            ->method('delete')
            ->will($this->returnSelf());

        $repositoryMock = $this->getMockBuilder('\Cops\Core\Entity\UserBookRepository')
            ->setConstructorArgs(array($this->app['config']))
            ->setMethods(array('getConnection'))
            ->getMock();

        $repositoryMock->expects($this->exactly(2))
            ->method('getConnection')
            ->will($this->returnValue($connectionMock));

        $userBook = $this->app['entity.user-book']->setRepository($repositoryMock);
        $userBook->save();
        $userBook->delete();
    }

    public function testFindMethods()
    {
        $qbMock = $this->getMockBuilder('\Cops\Core\Entity\UserBookRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'from', 'where', 'setParameter', 'andWhere', 'execute', 'fetchAll'))
            ->getMock();

        $qbMock->expects($this->any())
            ->method('select')
            ->will($this->returnSelf());
        $qbMock->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());
        $qbMock->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());
        $qbMock->expects($this->any())
            ->method('setParameter')
            ->will($this->returnSelf());
        $qbMock->expects($this->any())
            ->method('andWhere')
            ->will($this->returnSelf());
        $qbMock->expects($this->exactly(3))
            ->method('execute')
            ->will($this->returnSelf());
        $qbMock->expects($this->exactly(3))
            ->method('fetchAll')
            ->will($this->returnValue(array()));

        $repositoryMock = $this->getMockBuilder('\Cops\Core\Entity\UserBookRepository')
            ->setConstructorArgs(array($this->app['config']))
            ->setMethods(array('getQueryBuilder'))
            ->getMock();

        $repositoryMock->expects($this->exactly(3))
            ->method('getQueryBuilder')
            ->will($this->returnValue($qbMock));

        $collection = $this->app['collection.user-book']
            ->setRepository($repositoryMock);

        $collection->findFromUserId(1);
        $collection->findFromUserIdAndAction(1, 'dummy');
        $collection->findFromBookIdAndUserId(1, 1);

    }

    /**
     * Get user book repository
     * @return \Cops\Core\Èntity\UserBookRepository
     */
    protected function getUserBookRepository()
    {
        return $this->app['repository.user-book'];
    }
}
