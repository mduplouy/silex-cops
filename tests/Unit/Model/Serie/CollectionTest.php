<?php

namespace Cops\Tests\Model\Serie;

use Cops\Tests\AbstractTestCase;

/**
 * Book collection test cases
 *
 * @require PHP 5.3
 */
class CollectionTest extends AbstractTestCase
{
    public function testGetByFirstLetter()
    {
        $collection = $this->app['model.serie']->getCollection()->getByFirstLetter('H');

        $this->assertInstanceOf('\Cops\Model\Serie\Collection', $collection);
    }
}