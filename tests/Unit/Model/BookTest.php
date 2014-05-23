<?php

namespace Cops\Tests\Model;

use Silex\WebTestCase;

/**
 * Book model test cases
 *
 * @require PHP 5.3
 */
class BookTest extends WebTestCase
{
    private $emptyBook;
    private $book;

    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    private function getEmptyBook()
    {
        if (null === $this->emptyBook) {
            $this->emptyBook = $this->app['model.book'];
        }
        return $this->emptyBook;
    }

    private function getRealBook()
    {
        $book = $this->app['model.book'];
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

    public function testUpdateTitle()
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

    /**
     * @expectedException Cops\Exception\BookException
     */
    public function testLoadException()
    {
        $this->getEmptyBook()->load(-1);
    }

}
