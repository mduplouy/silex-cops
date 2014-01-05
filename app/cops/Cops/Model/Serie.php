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
     * Number of books
     * @var int
     */
    protected $bookCount;

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
        return $this->setData(
            $this->getResource()->load($serieId)
        );
    }

    /**
     * Get the download-safe flavour of the serie name
     *
     * @return string
     */
    public function getDownloadSafeName()
    {
        return $this->removeAccents($this->getName());
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
        return $this->getModel('Book')->getCollection()
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
        $this->id     = null;
        $this->name   = null;
        $this->sort   = null;
        $this->_books = null;
        parent::__clone();
    }
}