<?php

namespace Cops\Tests\Model\Book;

use Silex\WebTestCase;

/**
 * Book collection test cases
 *
 * @require PHP 5.3
 */
class CollectionTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../../application.php';
    }

    public function testCollection()
    {
        $collection = $this->app['model.book']->getCollection();

        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getLatest(10));
        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getByTagId(2));
        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getByKeyWord(array('any')));
        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getOtherBooksFromSerie());
        $this->assertInstanceOf('Cops\Model\Book\Collection', $collection->getOtherBooksFromAuthor());
    }


}
