<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

use Cops\Model\EntityAbstract;
use Cops\Model\Book;

/**
 * Serie model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Serie extends EntityAbstract
{
    /**
     * Object ID
     * @var int
     */
    protected $id;

    /**
     * Serie name
     * @var string
     */
    protected $name;

    /**
     * Sort name
     * @var string
     */
    protected $sort;

    /**
     * Number of books
     * @var int
     */
    protected $bookCount;

    /**
     * Book instance
     * @var \Cops\Model\Book
     */
    private $book;

    /**
     * Load serie
     *
     * @param int $serieId
     *
     * @return \Cops\Model\Serie
     */
    public function load($serieId)
    {
        return $this->setData(
            $this->getResource()->load($serieId)
        );
    }

    /**
     * Book setter
     *
     * @param Book
     *
     * @return $this
     */
    public function setBook(Book $book)
    {
        $this->book = $book;
        return $this;
    }

    /**
     * Get aggregated series by first letter
     *
     * @return array
     */
    public function getAggregatedList()
    {
        $series = array();
        foreach ($this->getResource()->getAggregatedList() as $serie) {
            // Force non alpha to #
            if (!preg_match('/[A-Z]/', $serie['first_letter'])) {
                $serie['first_letter'] = '#';
            }
            if (!array_key_exists($serie['first_letter'], $series)) {
                $series[$serie['first_letter']] = 0;
            }
            $series[$serie['first_letter']] += $serie['nb_serie'];
        }
        return $series;
    }

    /**
     * Get all books from serie
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getAllBooks()
    {
        return $this->app['model.book']->getCollection()
            ->getBySerieId($this->getId())
            ->addBookFiles();
    }

    /**
     * Get number of books in serie
     *
     * @return int
     */
    public function getNumberOfBooks()
    {
        if (is_null($this->bookCount)) {
            $this->bookCount = $this->getResource()->countBooks($this->getId());
        }
        return $this->bookCount;
    }

    /**
     * Reset data on clone
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();
        $this->id     = null;
        $this->name   = null;
        $this->sort   = null;
    }
}