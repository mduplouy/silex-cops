<?php

namespace Cops\Tests\Core\Search;

use Cops\Tests\AbstractTestCase;

/**
 * Search factory test
 *
 * @require PHP 5.3
 */
class SearchFactoryTest extends AbstractTestCase
{
    public function testService()
    {
        $factory = $this->app['factory.search'];

        $this->assertInstanceOf('\Cops\Core\Search\SearchFactory', $factory);
    }

    /**
     * @expectedException \Cops\Core\Search\WrongAdapterException
     */
    public function testGetInstanceException()
    {
        $factory = new \Cops\Core\Search\SearchFactory(array(
            'test' => function () {
                return new \stdClass;
            }
        ));

        $factory->getInstance('test');
    }

    public function testGetInstance()
    {
        $mock = $this->getMockBuilder('\Cops\Core\Search\AdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new \Cops\Core\Search\SearchFactory(array(
            'test' => function () use ($mock) {
                return $mock;
            }
        ));

        $this->assertInstanceOf(get_class($mock), $factory->getInstance('test'));
    }
}
