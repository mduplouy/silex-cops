<?php

namespace Cops\Tests\Core\Archive;

use Cops\Tests\AbstractTestCase;

/**
 * Image factory test
 *
 * @require PHP 5.3
 */
class ImageFactoryTest extends AbstractTestCase
{
    public function testService()
    {
        $factory = $this->app['factory.image'];

        $this->assertInstanceOf('\Cops\Core\Image\ImageFactory', $factory);
    }

    /**
     * @expectedException \Cops\Core\Image\WrongAdapterException
     */
    public function testGetInstanceException()
    {
        $factory = new \Cops\Core\Image\ImageFactory(array(
            'test' => function () {
                return new \stdClass;
            }
        ));

        $factory->getInstance('test');
    }

    public function testGetInstance()
    {
        $mock = $this->getMockBuilder('\Cops\Core\Image\AdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new \Cops\Core\Image\ImageFactory(array(
            'test' => function () use ($mock) {
                return $mock;
            }
        ));

        $this->assertInstanceOf(get_class($mock), $factory->getInstance('test'));
    }
}
