<?php

namespace Cops\Tests\Core;

use Cops\Tests\AbstractTestCase;

class AbstractCollectionTest extends AbstractTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetByIdException()
    {
        $collection = new \TestAbstractCollection\ConcreteCollection;
        $collection->getById('none');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetByKeyException()
    {
        $collection = new \TestAbstractCollection\ConcreteCollection;
        $collection->getByKey('none');
    }
}

namespace TestAbstractCollection;

class ConcreteCollection extends \Cops\Core\AbstractCollection
{
}
