<?php

namespace Cops\Tests\Model\ImageProcessor;

use Cops\Tests\AbstractTestCase;

class ImageProcessorFactoryTest extends AbstractTestCase
{
    /**
     * @expectedException \Cops\Exception\ImageProcessor\AdapterException
     */
    public function testGetInstanceException()
    {
        $factory = $this->app['factory.image']->getInstance('non-existent');
    }
}
