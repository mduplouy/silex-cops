<?php

namespace Cops\Tests\Model;

use Silex\WebTestCase;

/**
 * Book model test cases
 *
 * @require PHP 5.3
 */
class AuthorTest extends  WebTestCase
{
    protected $sourcePath;
    protected $targetPath;

    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    public function setUp()
    {
        parent::setUp();
        $this->sourcePath = __DIR__.'/../../';
        $this->targetPath = BASE_DIR.$this->app['config']->getValue('public_dir');
    }

    public function testLoadOk()
    {
        $author = $this->app['model.author']->load(4);

        $this->assertEquals($author->getId(), 4);
    }

    /**
     * @expectedException Cops\Exception\AuthorException
     */
    public function testLoadKo()
    {
        $author = $this->app['model.author']->load(1);

        $this->assertEquals($author->getId(), 1);
    }

    public function testLoadAllBooksCollection()
    {
        $author = $this->app['model.author']->load(4);
        $books = $author->getAllBooks();

        $this->assertInstanceOf('Cops\Model\Book\Collection', $books);
        $this->assertEquals($author->getNumberOfBooks(), $books->count());
    }

    public function testLoadByBookId()
    {
        $authors = $this->app['model.author']->getCollection()->getByBookId(5);
        $this->assertInstanceOf('Cops\Model\Author\Collection', $authors);
    }

    public function testLoadByFirstLetter()
    {
        $authors = $this->app['model.author']->getCollection()->getByFirstLetter('A');
        $this->assertInstanceOf('Cops\Model\Author\Collection', $authors);

        $authors = $this->app['model.author']->getCollection()->getByFirstLetter('#');
        $this->assertInstanceOf('Cops\Model\Author\Collection', $authors);
    }


    public function testCloneResetProperties()
    {
        $origAuthor = $this->app['model.author']->load(4);

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
        $author = $this->app['model.author'];
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
        $newId = $this->app['model.author']
            ->setName('My Author')
            ->setSort('Author, My')
            ->save();

        $this->assertGreaterThan(0, $newId);

        $author = $this->app['model.author'];
        $author->load($newId);

        $this->assertEquals($author->getName(), 'My Author');
        $this->assertEquals($author->getSort(), 'Author, My');

        $author->setName('John Doe')->save();

        $author->delete();
    }
}
