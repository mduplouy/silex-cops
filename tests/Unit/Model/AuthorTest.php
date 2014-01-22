<?php

namespace Cops\Tests\Model;

/**
 * Book model test cases
 *
 * @require PHP 5.2
 */
class AuthorTest extends \PHPUnit_Framework_TestCase
{
    protected $author;

    private function getEmptyauthor()
    {
        if (null === $this->author) {
            $this->author = new \Cops\Model\Author;
        }
        return $this->author;
    }

    public function testLoadOk()
    {
        $author = $this->getEmptyauthor()->load(4);

        $this->assertEquals($author->getId(), 4);
    }

    /**
     * @expectedException Cops\Exception\AuthorException
     */
    public function testLoadKo()
    {
        $author = $this->getEmptyAuthor()->load(1);

        $this->assertEquals($author->getId(), 1);
    }

    public function testLoadAllBooksCollection()
    {
        $author = $this->getEmptyauthor()->load(4);
        $books = $author->getAllBooks();

        $this->assertInstanceOf('Cops\Model\Book\Collection', $books);
        $this->assertEquals($author->getNumberOfBooks(), $books->count());
    }

    public function testLoadByFirstLetter()
    {
        $authors = $this->getEmptyauthor()->getCollection()->getByFirstLetter('A');
        $this->assertInstanceOf('Cops\Model\Author\Collection', $authors);

        $authors = $this->getEmptyauthor()->getCollection()->getByFirstLetter('#');
        $this->assertInstanceOf('Cops\Model\Author\Collection', $authors);
    }


    public function testCloneResetProperties()
    {
        $origAuthor = $this->getEmptyauthor();

        $origAuthor->load(4);

        $author = clone($origAuthor);

        $this->assertNull($author->getId(),   "Author Id not null after cloning");
        $this->assertNull($author->getName(), "Author pubdate not null after cloning");
        $this->assertNull($author->getSort(), "Author pubdate not null after cloning");

    }

}
