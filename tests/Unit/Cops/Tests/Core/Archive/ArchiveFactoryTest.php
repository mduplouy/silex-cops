<?php

namespace Cops\Tests\Core\Archive;

use Cops\Tests\AbstractTestCase;

/**
 * Archive factory test
 *
 * @require PHP 5.3
 */
class ArchiveFactoryTest extends AbstractTestCase
{
    public function testService()
    {
        $factory = $this->app['factory.archive'];

        $this->assertInstanceOf('\Cops\Core\Archive\ArchiveFactory', $factory);
    }

    /**
     * @expectedException \Cops\Core\Archive\WrongAdapterException
     */
    public function testGetInstanceException()
    {
        $factory = new \Cops\Core\Archive\ArchiveFactory(array(
            'test' => function () {
                return new \stdClass;
            }
        ));

        $factory->getInstance('test');
    }

    public function testGetInstance()
    {
        $mock = $this->getMockBuilder('\Cops\Core\Archive\AbstractAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new \Cops\Core\Archive\ArchiveFactory(array(
            'test' => function () use ($mock) {
                return $mock;
            }
        ));

        $this->assertInstanceOf(get_class($mock), $factory->getInstance('test'));
    }
}

