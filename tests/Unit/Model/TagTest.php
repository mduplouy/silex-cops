<?php

namespace Cops\Tests\Model;

use Cops\Model\Core;
use Silex\WebTestCase;

/**
 * Tag model test cases
 *
 * @require PHP 5.3
 */
class TagModelTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    public function testCountBookOk()
    {
        $tag = $this->app['model.tag'];
        $tag->load(7);
        $nbBook = $tag->getNumberOfBooks();
        $this->assertEquals(1, $nbBook);

        $tag = $this->app['model.tag'];
        $nbBook = $tag->getNumberOfBooks();
        $this->assertEquals(0, $nbBook);
    }

    /**
     * @dataProvider getNewTags
     *
     * @parma int    $bookId
     * @param string $tagName
     */
    public function testAssociateToBook($bookId, $tagName)
    {
        /**
         * @var Cops\Model\Tag
         */
        $tag = $this->app['model.tag'];
        $this->assertTrue($tag->associateToBook($bookId, $tagName), 'Failed to associate tagName to bookId');
    }

    /**
     * New tag data provider
     */
    public function getNewTags()
    {
        return array(
            array(5, 'test-tag'),
            array(5, 'another-tag'),
            array(5, 'last-tag'),
        );
    }

    public function testAssociateToBookWithTagObject()
    {
        /**
         * @var Cops\Model\Tag
         */
        $tag = $this->app['model.tag'];
        $tag->setName('dummy-tag');

        $this->assertTrue($tag->associateToBook(5), 'Tag::AssociateToBook did not return boolean true');
    }

    /**
     * @depends testAssociateToBookWithTagObject
     */
    public function testLoadByNameOk()
    {
        /**
         * @var Cops\Model\Tag
         */
        $tag = $this->app['model.tag'];
        $tag->loadByName('dummy-tag');

        $this->assertEquals($tag->getName(), 'dummy-tag', 'Unable to retrieve tag from name');
    }

    /**
     * @depends testLoadByNameOk
     */
    public function testDeleteFromBook()
    {
        /**
         * @var Cops\Model\Tag
         */
        $tag = $this->app['model.tag'];
        $this->assertTrue($tag->deleteFromBook(5), 'Delete from book did not returned boolean true');

        $book = $this->app['model.book'];
        $book->load(5);

        $this->assertEquals(0, $book->getTags()->count(), 'Delete from book failed');
    }

}