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

    public function setUp()
    {
        $this->app = \Cops\Model\Core::getApp();
    }

    private function getEmptyBook()
    {
        if (null === $this->emptyBook) {
            $this->emptyBook = new \Cops\Model\Book($this->app);
        }
        return $this->emptyBook;
    }

    private function getRealBook()
    {
        $book = new \Cops\Model\Book($this->app);
        $book->load(3);
        return $book;
    }

    public function testBasicGetters()
    {
        $book = $this->getEmptyBook();

        $this->assertInstanceOf('Cops\Model\Cover', $book->getCover());
        $this->assertInstanceOf('Cops\Model\Serie', $book->getSerie());
        $this->assertInstanceOf('Cops\Model\Author\Collection', $book->getAuthors());
        $this->assertInstanceOf('Cops\Model\BookFile\Collection', $book->getFiles());
        $this->assertFalse($book->hasCover());
    }

    /**
     * @expectedException Cops\Exception\BookFile\FormatUnavailableException
     */
    public function testBasicGettersException()
    {
        $book = $this->getEmptyBook();
        $this->assertInstanceOf('Cops\Model\BookFile\BookFileInterface', $book->getFile());
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
     * @expectedException Cops\Exception\BookException
     */
    public function testLoadException()
    {
        $this->getEmptyBook()->load(-1);
    }

}
