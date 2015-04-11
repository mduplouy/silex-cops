<?php

namespace Cops\Tests\Core\Archive;

use Cops\Tests\AbstractTestCase;

/**
 * Abstract adapter test
 *
 * @require PHP 5.3
 */
class ArchiveAdapterTest extends AbstractTestCase
{
    public function testAddingFiles()
    {
        $mock = $this->getMock('\Cops\Core\Entity\BookFile\BookFileCollection');
        $mock->expects($this->exactly(3))
            ->method('add')
            ->will($this->returnValue('self'));

        $adapter = new \TestArchiveAbstractAdapter\ConcreteAdapter($mock);

        // Test adding single file
        $bookfileMock = $this->getMockBuilder('\Cops\Core\Entity\BookFile\AdapterInterface')
            ->getMock();
        $adapter->addFile($bookfileMock);

        // Test adding file collection
        $bookfilesMock = $this->getMockBuilder('\Cops\Core\Entity\BookFile\BookFileCollection')
            ->enableOriginalClone()
            ->getMock();

        $bookfilesMock->expects($this->once())
            ->method('getIterator')
            ->will(
                $this->returnValue(
                    new \ArrayIterator(array($bookfileMock, $bookfileMock))
                )
            );

        $adapter->addFiles($bookfilesMock);

        // Set files but unable to check
        $adapter->setFiles($bookfilesMock);
    }

}

namespace TestArchiveAbstractAdapter;

class ConcreteAdapter extends \Cops\Core\Archive\AbstractAdapter
{
    public function getExtension()
    {
    }

    public function generateArchive()
    {
    }
}
