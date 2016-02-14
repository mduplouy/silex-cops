<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * Book entity
 *
 * @require PHP 5.3
 */
class EditableBookTest extends AbstractTestCase
{
    /**
     * Get book
     * @return \Cops\Core\Èntity\Book
     */
    protected function getBook()
    {
        return $this->app['entity.book'];
    }


    public function testUpdateAuthor()
    {
        $repositoryMock = $this->getMockBuilder('\Cops\Core\Entity\Book\EditableBookRepository')
            ->setMethods(array('updateAuthor'))
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('updateAuthor')
            ->with(1, array('John Doe'));

        $this->getBook()
            ->setId(1)
            ->setRepository($repositoryMock)
            ->updateAuthor('John Doe');
    }

    public function testUpdateTitle()
    {
        $repositoryMock = $this->getMockBuilder('\Cops\Core\Entity\Book\EditableBookRepository')
            ->setMethods(array('updateTitle'))
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('updateTitle')
            ->with(1, 'Dummy Title');

        $this->getBook()
            ->setId(1)
            ->setRepository($repositoryMock)
            ->updateTitle('Dummy Title');
    }

    public function testUpdatePubDate()
    {
        $repositoryMock = $this->getMockBuilder('\Cops\Core\Entity\Book\EditableBookRepository')
            ->setMethods(array('updatePubDate'))
            ->getMock();

        $dateTime = new \DateTime;

        $repositoryMock->expects($this->once())
            ->method('updatePubDate')
            ->with(1, $dateTime);

        $this->getBook()
            ->setId(1)
            ->setRepository($repositoryMock)
            ->updatePubDate($dateTime);
    }

    public function testUpdateComment()
    {
        $repositoryMock = $this->getMockBuilder('\Cops\Core\Entity\Book\EditableBookRepository')
            ->setMethods(array('updateComment'))
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('updateComment')
            ->with(1, 'Dummy comment');

        $this->getBook()
            ->setId(1)
            ->setRepository($repositoryMock)
            ->updateComment('Dummy comment');
    }
}
