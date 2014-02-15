<?php

namespace Cops\Tests\Model;

/**
 * Tag model test cases
 *
 * @require PHP 5.3
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    public function testCountBookOk()
    {
        $tag = new \Cops\Model\Tag;
        $tag->setId(7);
        $nbBook = $tag->getNumberOfBooks();
        $this->assertEquals(1, $nbBook);

        $tag = new \Cops\Model\Tag;
        $nbBook = $tag->getNumberOfBooks();
        $this->assertEquals(0, $nbBook);

    }

}