<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * Serie collection
 *
 * @require PHP 5.3
 */
class SerieCollectionTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Entity\SerieCollection', $this->getCollection());
    }

    public function testCountGroupedByFirstLetter()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('countGroupedByFirstLetter')
            ->will($this->returnValue(array(
                array(
                    'first_letter' => 'A',
                    'nb_serie'     => 2,
                ),
                array(
                    'first_letter' => '9',
                    'nb_serie'     => 3,
                ),
            )));

        $serieCount = $this->getCollection()
            ->setRepository($mock)
            ->countGroupedByFirstLetter(array());

        $this->assertEquals($serieCount, array(
            '#' => 3,
            'A' => 2,
        ));
    }

    public function testFindByFirstLetter()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('findByFirstLetter')
            ->with('A',array())
            ->will($this->returnValue(array()));

        $this->getCollection()
            ->setRepository($mock)
            ->findByFirstLetter('A',array());
    }

    public function testCountAll()
    {
        $mock = $this->getMockRepository();
        $mock->expects($this->once())
            ->method('countAll')
            ->will($this->returnValue(2));

        $this->getCollection()
            ->setRepository($mock)
            ->countAll();
    }

    /**
     * Get serie
     * @return \Cops\Core\Èntity\SerieCollection
     */
    protected function getCollection()
    {
        return $this->app['collection.serie'];
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
