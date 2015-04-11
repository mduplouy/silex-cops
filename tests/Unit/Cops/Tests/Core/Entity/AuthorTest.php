<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * Author
 *
 * @require PHP 5.3
 */
class AuthorTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Entity\Author', $this->getAuthor());
    }

    /**
     * @expectedException \Cops\Core\Entity\Exception\AuthorNotFoundException
     */
    public function testFindByIdException()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue(array()));

        $this->getAuthor()->setRepository($mock)->findById(1);
    }

    public function testFindById()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue(array(
                'id'         => '1',
                'name'       => 'dummy',
                'sort'       => 'dummy sort',
                'link'       => '',
            )));

        $author = $this->getAuthor()->setRepository($mock)->findById(1);

        $this->assertEquals($author->getName(), 'dummy');
        $this->assertEquals($author->getSort(), 'dummy sort');
    }

    public function testSave()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('save')
            ->will($this->returnValue(1));

        $save = $this->getAuthor()->setRepository($mock)->save();

        $this->assertEquals($save, 1);
    }

    public function testDelete()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));

        $delete = $this->getAuthor()->setRepository($mock)->delete();

        $this->assertTrue($delete);
    }

    public function testSetName()
    {
        $author = $this->getAuthor()->setName('lastname|firstname');

        $this->assertEquals($author->getName(), 'firstname lastname');
    }

    /**
     * Get Author
     * @return \Cops\Core\Èntity\Author
     */
    protected function getAuthor()
    {
        return $this->app['entity.author'];
    }

    /**
     * Get mock repository
     */
    protected function getMockRepository()
    {
        return $this->getMockBuilder('\Cops\Core\Entity\AuthorRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
