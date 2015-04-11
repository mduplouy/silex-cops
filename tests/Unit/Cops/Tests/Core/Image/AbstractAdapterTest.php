<?php

namespace Cops\Tests\Core\Image;

use Cops\Tests\AbstractTestCase;

/**
 * Abstract adapter test
 *
 * @require PHP 5.3
 */
class ArchiveAdapterTest extends AbstractTestCase
{
    public function testWholeClass()
    {
        $mockConfig = $this->getMockBuilder('\Cops\Core\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $mockConfig->expects($this->exactly(2))
            ->method('getValue')
            ->will($this->returnValue(100));

        $adapter = new \TestImageAbstractAdapter\ConcreteAdapter($mockConfig);

        $this->assertEquals($adapter->getWidth(), 100);
        $this->assertEquals($adapter->getHeight(), 100);

        $adapter->setHeight('abcd');
        $this->assertEquals(0, $adapter->getHeight(), 'Integer cast missing on setHeight()');

        $adapter->setWidth('abcd');
        $this->assertEquals(0, $adapter->getWidth(), 'Integer cast missing on setWidth()');

    }

}

namespace TestImageAbstractAdapter;

class ConcreteAdapter extends \Cops\Core\Image\AbstractAdapter
{
    public function generateThumbnail($src, $dst, array $params = array())
    {
    }
}
