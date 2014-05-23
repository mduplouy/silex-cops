<?php

namespace Cops\Tests\Model;

/**
 * Tag model test cases
 *
 * @require PHP 5.3
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = \Cops\Model\Core::getApp();
    }

    public function testCountBookOk()
    {
        $tag = new \Cops\Model\Tag($this->app);
        $tag->setId(7);
        $nbBook = $tag->getNumberOfBooks();
        $this->assertEquals(1, $nbBook);

        $tag = new \Cops\Model\Tag($this->app);
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
        $this->assertTrue($tag->associateToBook($bookId, $tagName));
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

        $this->assertTrue($tag->associateToBook(5));
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

        $this->assertEquals($tag->getName(), 'dummy-tag');
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
        $this->assertTrue($tag->deleteFromBook(5));

        $book = $this->app['model.book'];
        $book->load(5);

        $this->assertEquals(0, $book->getTags()->count());
    }

}