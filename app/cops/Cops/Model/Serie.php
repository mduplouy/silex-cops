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

/**
 * Serie model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Serie extends Common
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
     * Books collection
     * @var \Cops\Model\Book\Collection
     */
    private $_books;

    /**
     * Load serie
     *
     * @param int $serieId
     *
     * @return \Cops\Model\Serie
     */
    public function load($serieId)
    {
        return $this->getResource()->load($serieId, $this);
    }

    /**
     * Get aggregated series by first letter
     *
     * @return array
     */
    public function getAggregatedList()
    {
        $series = array();
        foreach($this->getResource()->getAggregatedList() as $serie) {
            // Force non alpha to #
            if (!preg_match('/[A-Z]/', $serie['first_letter'])) {
                $serie['first_letter'] = '#';
            }
            $series[$serie['first_letter']] = $serie['nb_serie'];
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
        $book = $this->getModel('Book')->getCollectionBySerieId($this->getId());
    }

    /**
     * Get number of books in serie
     *
     * @return int
     */
    public function getNumberOfBooks()
    {
        return $this->getResource()->countBooks($this->getId());
    }

    /**
     * Get series filtered by first letter of serie name
     *
     * @param string|0 $letter
     *
     * @return \Cops\Model\Serie\Collection
     */
    public function getCollectionByFirstLetter($letter)
    {
        return $this->getResource()->getCollectionByFirstLetter($letter, $this);
    }
}