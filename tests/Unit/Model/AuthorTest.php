<?php

namespace Cops\Tests\Model;

/**
 * Book model test cases
 *
 * @require PHP 5.3
 */
class AuthorTest extends \PHPUnit_Framework_TestCase
{
    protected $author;

    protected $app;

    public function setUp()
    {
        $this->app = \Cops\Model\Core::getApp();
        $this->sourcePath = __DIR__.'/../../';
        $this->targetPath = BASE_DIR.$this->app['config']->getValue('public_dir');
    }

    private function getEmptyauthor()
    {
        if (null === $this->author) {
            $this->author = new \Cops\Model\Author($this->app);
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

    public function testLoadByBookId()
    {
        $authors = $this->getEmptyauthor()->getCollection()->getByBookId(5);
        $this->assertInstanceOf('Cops\Model\Author\Collection', $authors);
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

    /**
     * @dataProvider getDataForAuthorSort
     */
    public function testAuthorSort($authorName, $expectedSort)
    {
        $author = $this->getEmptyauthor();
        $config = $this->app['config'];

        $author->setSort(null)->setName($authorName);

        $this->assertEquals(
            $author->getSort(),
            $expectedSort,
            sprintf(
                'Default sort algorithm gives %s result instead of %s',
                $author->getSort(),
                $expectedSort
            )
        );
    }

    /**
     * Data provider for author sort testing
     */
    public function getDataForAuthorSort()
    {
        return array(
            array('John Smith',       'Smith, John'),
            array('John Steve Smith', 'Smith, John Steve'),
            array('Smith',            'Smith'),
        );
    }

    public function testInsert()
    {
        $author = new \Cops\Model\Author($this->app);
        $newId = $author
            ->setName('My Author')
            ->setSort('Author, My')
            ->save();

        $this->assertGreaterThan(0, $newId);

        $author = new \Cops\Model\Author($this->app);
        $author->load($newId);

        $this->assertEquals($author->getName(), 'My Author');
        $this->assertEquals($author->getSort(), 'Author, My');

        $author->setName('John Doe')->save();

        $author->delete();
    }
}
