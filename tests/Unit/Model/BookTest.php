<?php

namespace Cops\Tests\Model;

/**
 * Book model test cases
 *
 * @require PHP 5.3
 */
class BookTest extends \PHPUnit_Framework_TestCase
{

    private $emptyBook;
    private $book;

    private function getEmptyBook()
    {
        if (null === $this->emptyBook) {
            $this->emptyBook = new \Cops\Model\Book;
        }
        return $this->emptyBook;
    }

    private function getRealBook()
    {
        $book = new \Cops\Model\Book;
        $book->load(3);
        return $book;
    }

    public function testBasicGetters()
    {
        $book = $this->getEmptyBook();

        $this->assertInstanceOf('Cops\Model\Cover', $book->getCover());
        $this->assertInstanceOf('Cops\Model\Serie', $book->getSerie());
        $this->assertInstanceOf('Cops\Model\Author\Collection', $book->getAuthors());
        $this->assertInstanceOf('Cops\Model\BookFile\BookFileInterface', $book->getFile());
        $this->assertInternalType('array', $book->getFiles());
        $this->assertFalse($book->hasCover());
    }

    public function testCloneResetProperties()
    {
        $origBook = $this->getRealBook();

        $origBook->load(5);

        $book = clone($origBook);

        $this->assertTrue($book->getId() === null,      "Book Id not null after cloning");
        $this->assertTrue($book->getPubdate() === null, "Book pubdate not null after cloning");
        $this->assertTrue($book->hasCover() === false,  "Book hasCover not false after cloning");
        $this->assertTrue($book->getPath() === null);
        $this->assertTrue($book->getComment() === null);
    }

    public function testUpdateAuthor()
    {
        $origBook = $this->getRealBook();

        // Check there is 1 author
        $origAuthors = $origBook->getAuthors();
        $this->assertEquals(1, $origAuthors->count());

        // Check now there are 2
        $origBook->updateAuthor('John Smith & Jane Doe');
        $modifiedBook = $this->getRealBook();
        $this->assertEquals(2, $modifiedBook->getAuthors()->count());

        // Revert back to original author
        $origBook->updateAuthor($origAuthors->getName());
    }

    /**
     * @expectedException Cops\Exception\BookException
     */
    public function testLoadException()
    {
        $this->getEmptyBook()->load(-1);
    }

}
