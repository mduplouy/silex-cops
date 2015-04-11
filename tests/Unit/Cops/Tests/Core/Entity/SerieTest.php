<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * Serie
 *
 * @require PHP 5.3
 */
class SerieTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Entity\Serie', $this->getSerie());
    }

    /**
     * @expectedException \Cops\Core\Entity\Exception\SerieNotFoundException
     */
    public function testFindByIdException()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue(array()));

        $this->getSerie()
            ->setRepository($mock)
            ->findById(1);
    }

    public function testLoad()
    {
        $data = array(
            'id'         => '1',
            'name'       => 'dummy name',
            'sort'       => 'dummy sort',
            'book_count' => '3',
        );

        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findById')
            ->will($this->returnValue($data));

        $this->getSerie()
            ->setRepository($mock)
            ->findById(1);

    }

    public function testGetBookCount()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('countBooks')
            ->will($this->returnValue(2));

        $serie = $this->getSerie()
            ->setId(5)
            ->setRepository($mock);

        $serie->getBookCount();

        // Second time repo must not be called
        $serie->getBookCount();
    }

    /**
     * Get serie
     * @return \Cops\Core\Èntity\Serie
     */
    protected function getSerie()
    {
        return $this->app['entity.serie'];
    }

    /**
     * Get mock repository
     */
    protected function getMockRepository()
    {
        return $this->getMockBuilder('\Cops\Core\Entity\SerieRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
