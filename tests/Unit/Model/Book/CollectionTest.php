<?php

namespace Cops\Tests\Model\Book;

/**
 * Book collection test cases
 *
 * @require PHP 5.2
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $book = new \Cops\Model\Book;

        $collection = $book->getCollection();

        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getLatest(10));
        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getByTagId(2));
        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getByKeyWord(array('any')));
        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getOtherBooksFromSerie());
        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getOtherBooksFromAuthor());
    }


}
