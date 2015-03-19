<?php

namespace Cops\Tests\Core;

use Cops\Tests\AbstractTestCase;

/**
 * Abstract factory test
 *
 * @require PHP 5.3
 */
class AbstractBookCountTest extends AbstractTestCase
{
    public function testGetBookCount()
    {
        $mock = $this->getMockBuilder('\Cops\Core\AbstractRepository')
            ->setMethods(array('countBooks'))
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('countBooks')
            ->will($this->returnValue(1));

        $concrete = new \TestAbstractBookCount\ConcreteBookCount;

        $concrete->setRepository($mock)->setId(1);

        $this->assertEquals($concrete->getBookCount(),  1);

        // No further call to repository
        $this->assertEquals($concrete->getBookCount(),  1);

        // Still no call when book count is set
        $concrete = new \TestAbstractBookCount\ConcreteBookCount;
        $concrete->setRepository($mock)->setBookCount(2);
        $this->assertEquals(2, $concrete->getBookCount());
    }
}

namespace TestAbstractBookCount;

class ConcreteBookCount extends \Cops\Core\AbstractBookCount
{
}
