<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * Book entity
 *
 * @require PHP 5.3
 */
class BookTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Entity\Book', $this->getBook());
    }

    /**
     * @expectedException \Cops\Core\Entity\Exception\BookNotFoundException
     */
    public function testFindByIdException()
    {
        $mock = $this->getMock('\Cops\Core\Entity\BookRepository');
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue(array()));

        $this->getBook()->setRepository($mock)->findById(1);
    }

    public function testSetSerieData()
    {
        $data = array(
            'serie_id'      => '1',
            'serie_name'    => 'dummy serie name',
            'serie_sort'    => 'dummy serie sort',
        );

        $serieMock = $this->getMockBuilder('\Cops\Core\Entity\Serie')
            ->setMethods(array('setId', 'setName', 'setSort'))
            ->disableOriginalConstructor()
            ->getMock();

        // why does once() fails ?
        $serieMock->expects($this->any())
            ->method('setId')
            ->with($data['serie_id']);

        $serieMock->expects($this->once())
            ->method('setName')
            ->with($data['serie_name']);
        $serieMock->expects($this->once())
            ->method('setSort')
            ->with($data['serie_sort']);

        $book = $this->getBook();

        $book->setSerie($serieMock)
            ->setDataFromArray($data);
    }

    public function testFindByIdAndDatabaseMapping()
    {
        $data = array(
            'pubdate'       => '2009-02-01 22:23:24+00:00',
            'title'         => 'dummy title',
            'sort'          => 'dummy sort',
            'cover_present' => '1',
            'path'          => 'dummy path',
            'comment'       => 'dummy comment',
            'last_modified' => '2010-01-02 12:13:14.598308+00:00',
            'series_index'  => '1.0',
            'isbn'          => 'dummy isbn',
            'lccn'          => 'dummy lccn',
            'flags'         => '1',
            'uuid'          => 'c96893dc-fd48-487a-9d1a-1c4123ee16ef',
            'serie_id'      => '1',
            'serie_name'    => 'dummy serie name',
            'serie_sort'    => 'dummy serie sort',
        );

        $mock = $this->getMock('\Cops\Core\Entity\BookRepository');
        $mock->expects($this->once())
            ->method('findById')
            ->with(1)
            ->will($this->returnValue($data));

        $book = $this->getBook()
            ->setRepository($mock)
            ->findById(1);

        $this->assertInstanceOf('\DateTime', $book->getPubDate());
        $this->assertEquals($book->getPubDate()->format('Y-m-d'), '2009-02-01');
        $this->assertEquals($book->getTitle(), $data['title']);
        $this->assertEquals($book->getSort(),  $data['sort']);
        $this->assertTrue($book->hasCover());
        $this->assertEquals($book->getPath(), $data['path']);
        $this->assertEquals($book->getComment(), $data['comment']);
        $this->assertInstanceOf('\DateTime', $book->getLastModified());
        $this->assertEquals($book->getLastModified()->format('Y-m-d'), '2010-01-02');
        $this->assertEquals($book->getSeriesIndex(), $data['series_index']);
        $this->assertEquals($book->getIsbn(), $data['isbn']);
        $this->assertEquals($book->getLccn(), $data['lccn']);
        $this->assertTrue($book->getFlags());
        $this->assertEquals($book->getUuid(), $data['uuid']);

        $this->assertInstanceOf('\Cops\Core\Entity\Serie', $book->getSerie());
        $this->assertEquals($book->getSerie()->getId(), $data['serie_id']);
        $this->assertEquals($book->getSerie()->getName(), $data['serie_name']);
        $this->assertEquals($book->getSerie()->getSort(), $data['serie_sort']);

        $this->assertInstanceOf('\Cops\Core\Entity\AuthorCollection', $book->getAuthors());
        $this->assertInstanceOf('\Cops\Core\Cover', $book->getCover());
        $this->assertInstanceOf('\Cops\Core\Entity\TagCollection', $book->getTags());
        $this->assertInstanceOf('\Cops\Core\Entity\BookFile\BookFileCollection', $book->getFiles());
    }

    public function testGetFile()
    {
        $book = $this->getBook();
        $book->setId(3);

        $bookFilesMock = $this->getMockBuilder('\Cops\Core\Entity\BookFile\BookFileCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $bookFilesMock->expects($this->once())
            ->method('findByFormatAndBookId')
            ->with(null, $book->getId());

        $book->setBookFileCollection($bookFilesMock)->getFile();
    }

    public function testGetCoverSetsInfoOnlyOnce()
    {
        $coverMock = $this->getMockBuilder('\Cops\Core\Cover')
            ->disableOriginalConstructor()
            ->getMock();

        $coverMock->expects($this->once())
            ->method('setBookInfo');

        $coverMock->expects($this->exactly(2))
            ->method('getBookId')
            ->will($this->onConsecutiveCalls(null, 5));

        $book = $this->getBook();
        $book->setId(5)->setCover($coverMock);

        $book->getCover();
        $book->getCover();
    }

    public function notestUpdateAuthor()
    {
        $book = $this->getBook()->findById(5);
        // Check there is 1 author
        $origAuthors = $origBook->getAuthors();
        $origNames = $origAuthors->getName();
        $this->assertEquals(1, $origAuthors->count());

        // Check now there are 2
        $origBook->updateAuthor('John Smith & Jane Doe');
        $modifiedBook = $this->getRealBook();
        $this->assertEquals(2, $modifiedBook->getAuthors()->count());

        // Revert back to original author
        $origBook->updateAuthor($origNames);
    }

    /**
     * Get book
     * @return \Cops\Core\Èntity\Book
     */
    protected function getBook()
    {
        return $this->app['entity.book'];
    }

    public function BasicGetters()
    {
        $book = $this->getBook();

        $this->assertInstanceOf('Cops\Model\Cover', $book->getCover());
        $this->assertInstanceOf('Cops\Model\Serie', $book->getSerie());
        $this->assertInstanceOf('Cops\Model\Author\Collection', $book->getAuthors());
        $this->assertInstanceOf('Cops\Model\BookFile\Collection', $book->getFiles());
        $this->assertFalse($book->hasCover());
    }

    public function UpdateAuthor()
    {
        $origBook = $this->getRealBook();

        // Check there is 1 author
        $origAuthors = $origBook->getAuthors();
        $origNames = $origAuthors->getName();
        $this->assertEquals(1, $origAuthors->count());

        // Check now there are 2
        $origBook->updateAuthor('John Smith & Jane Doe');
        $modifiedBook = $this->getRealBook();
        $this->assertEquals(2, $modifiedBook->getAuthors()->count());

        // Revert back to original author
        $origBook->updateAuthor($origNames);
    }

    public function UpdateTitle()
    {
        $book = $this->getRealBook();

        $origTitle = $book->getTitle();

        $this->assertTrue(
            $book->updateTitle('dummy-title'),
            'Book::updateTitle() failed'
        );

        $this->assertEquals(
            $book->getTitle(),
            'dummy-title',
            'Book setTitle() non consistent after Book::updateTitle() call'
        );

        // Revert back original title
        $this->assertTrue(
            $book->updateTitle($origTitle),
            'Book::updateTitle() failed'
        );
    }


}
