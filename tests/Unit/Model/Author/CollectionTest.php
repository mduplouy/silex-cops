<?php

namespace Cops\Tests\Model\Author;

use Silex\WebTestCase;

/**
 * Author collection test cases
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
        $collection = $this->app['model.author']->getCollection();

        $this->assertInstanceOf('Cops\Model\Author\Collection', $collection->getByFirstLetter('H'));
        $this->assertInstanceOf('Cops\Model\Author\Collection', $collection->getByBookId(3));
    }


}
