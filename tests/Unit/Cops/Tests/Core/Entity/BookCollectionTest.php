<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * Book collection
 *
 * @require PHP 5.3
 */
class BookCollectionTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Entity\BookCollection', $this->getCollection());
    }

    public function testGetLatest()
    {
        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('setMaxResults')
            ->will($this->returnSelf());
        $repoMock->expects($this->once())
            ->method('findSortedByDate')
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($repoMock)
            ->getLatest(5);
    }

    public function testFindSortedByDate()
    {
        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('findSortedByDate')
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($repoMock)
            ->findSortedByDate();
    }

    public function testCountAll()
    {
        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('countAll')
            ->will($this->returnValue(12));

        $count = $this->getCollection()
            ->setRepository($repoMock)
            ->countAll();

        $this->assertEquals($count, 12);
    }

    public function testFindAll()
    {
        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($repoMock)
            ->findAll();
    }

    public function testFindByAuthorId()
    {
        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('findByAuthorId')
            ->with(5)
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($repoMock)
            ->findByAuthorId(5);
    }

    public function testFindBySerieId()
    {
        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('findBySerieId')
            ->with(5)
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($repoMock)
            ->findBySerieId(5);
    }

    public function testFindByTagId()
    {
        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('findByTagId')
            ->with(5)
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($repoMock)
            ->findByTagId(5);
    }

    /**
     * @expectedException \Cops\Core\Entity\Exception\BookNotFoundException
     */
    public function testFindByKeywordException()
    {
        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('findByKeyword')
            ->with(array('one', 'two'))
            ->will($this->returnValue(array()));

        $count = $this->getCollection()
            ->setRepository($repoMock)
            ->findByKeyword(array('one', 'two'));
    }

    public function testFindByKeyword()
    {
        $data = array(
            array('id' => 2),
            array('id' => 3),
        );

        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('findByKeyword')
            ->with(array('one', 'two'))
            ->will($this->returnValue($data));

        $count = $this->getCollection()
            ->setRepository($repoMock)
            ->findByKeyword(array('one', 'two'));
    }

    public function testFindOthersFromSameAuthor()
    {
        $authorMock = $this->getMockBuilder('\Cops\Core\AbstractCollection')
            ->setMethods(array('count', 'getAllIds'))
            ->getMock();

        $authorMock->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));
        $authorMock->expects($this->once())
            ->method('getAllIds')
            ->will($this->returnValue(array(1, 2)));

        $bookMock = $this->getMockBuilder('\Cops\Core\Entity\Book')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'getSerie', 'getAuthors'))
            ->getMock();

        $bookMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(2));
        $bookMock->expects($this->once())
            ->method('getSerie')
            ->will($this->returnSelf());
        $bookMock->expects($this->once())
            ->method('getAuthors')
            ->will($this->returnValue($authorMock));

        $repoMock = $this->getMockRepository();

        $repoMock->expects($this->once())
            ->method('setExcludedBookId')
            ->will($this->returnSelf());
        $repoMock->expects($this->once())
            ->method('setExcludedSerieId');
        $repoMock->expects($this->once())
            ->method('findByAuthorId')
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($repoMock)
            ->findOthersFromSameAuthor($bookMock);
    }

    public function testFindOthersFromSameSerie()
    {
        $bookMock = $this->getMockBuilder('\Cops\Core\Entity\Book')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'getSerie'))
            ->getMock();

        $bookMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(2));
        $bookMock->expects($this->once())
            ->method('getSerie')
            ->will($this->returnSelf());

        $repoMock = $this->getMockRepository();
        $repoMock->expects($this->once())
            ->method('setExcludedBookId');
        $repoMock->expects($this->once())
            ->method('findBySerieId')
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($repoMock)
            ->findOthersFromSameSerie($bookMock);
    }

    /**
     * Get collection
     * @return \Cops\Core\Èntity\BookCollection
     */
    protected function getCollection()
    {
        return $this->app['collection.book'];
    }

    /**
     * Get mock repository
     */
    protected function getMockRepository()
    {
        return $this->getMockBuilder('\Cops\Core\Entity\BookRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
