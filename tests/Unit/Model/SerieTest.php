<?php

namespace Cops\Tests\Model;

use Silex\WebTestCase;

/**
 * Serie model test cases
 *
 * @require PHP 5.3
 */
class SerieTest extends WebTestCase
{

    protected $serie;

    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    private function getEmptySerie()
    {
        if (null === $this->serie) {
            $this->serie = $this->app['model.serie'];
        }
        return $this->serie;
    }

    public function testLoad()
    {
        $serie = $this->getEmptySerie()->load(1);

        $this->assertEquals($serie->getId(), 1);
    }

    /**
     * @expectedException Cops\Exception\SerieException
     */
    public function testLoadException()
    {
        $serie = $this->getEmptySerie()->load(-1);
    }

    public function testDownloadSafeName()
    {
        $serie = $this->getEmptySerie();
        $serie->setName('aéà');
        $this->assertEquals($serie->getDownloadSafeName(), 'aea');
    }

    public function testLoadAllBooksCollection()
    {
        $serie = $this->getEmptySerie()->load(1);
        $books = $serie->getAllBooks();

        $this->assertInstanceOf('Cops\Model\Book\Collection', $books);
        $this->assertEquals($serie->getNumberOfBooks(), $books->count());
    }

    public function testGetAggregatedList()
    {
        $seriesArray = $this->getEmptySerie()->getAggregatedList();
        $this->assertTrue(is_array($seriesArray));
    }

    public function testLoadByFirstLetter()
    {
        $series = $this->getEmptySerie()->getCollection()->getByFirstLetter('A');
        $this->assertInstanceOf('Cops\Model\Serie\Collection', $series);

        $series = $this->getEmptySerie()->getCollection()->getByFirstLetter('#');
        $this->assertInstanceOf('Cops\Model\Serie\Collection', $series);
    }

    public function testCloneResetProperties()
    {
        $origSerie = $this->getEmptySerie();

        $origSerie->load(1);

        $serie = clone($origSerie);

        $this->assertTrue($serie->getId() === null,   "Serie Id not null after cloning");
        $this->assertTrue($serie->getName() === null, "Serie pubdate not null after cloning");
        $this->assertTrue($serie->getSort() === null, "Serie pubdate not null after cloning");
    }
}
