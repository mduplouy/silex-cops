<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Model\Book;

use Cops\Model\Book;
use Cops\Model\CollectionAbstract;

/**
 * Book collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Collection extends CollectionAbstract implements \IteratorAggregate, \Countable
{
    /**
     * Remove current book from collections
     * @var bool
     */
    private $_removeCurentBook = false;

    /**
     * Get last added books
     *
     * @param int  $nb     Number of items to load
     *
     * @return Collection
     */
    public function getLatest($nb)
    {
        $resource = $this->getResource();

        foreach($resource->loadLatest($nb) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Get book collection based on author
     * Remove current book from collection
     *
     * @return Collection
     */
    public function getOtherBooksFromAuthor()
    {
        $this->_removeCurentBook = true;
        return $this->getByAuthorId();
    }

    /**
     * Get other books from author
     *
     * @param int|null     $authorId
     *
     * @return Collection
     */
    public function getByAuthorId($authorId = null)
    {
        $book = $this->getEntity();
        if ($authorId === null) {
            $authorId = $book->getAuthor()->getId();
        }

        $resource = $this->getResource();

        foreach($resource->loadByAuthorId($authorId) as $result) {
            if ($this->_removeCurentBook === true && $result['id'] == $book->getId()) {
                continue;
            }
            $this->add($resource->setDataFromStatement($result));
        }

        return $this;
    }

    /**
     * Get book collection based on a serie
     * Remove current book from collection
     *
     * @return Collection
     */
    public function getOtherBooksFromSerie()
    {
        $this->_removeCurentBook = true;
        return $this->getBySerieId();
    }

    /**
     * Get book collection based on a serie
     *
     * @param int|null     $serieId
     *
     * @return Collection
     */
    public function getBySerieId($serieId = null)
    {
        $book = $this->getEntity();
        if ($serieId === null) {
            $serieId = $book->getSerie()->getId();
        }

        $resource = $this->getResource();

        foreach($resource->loadBySerieId($serieId) as $result) {
            if ($this->_removeCurentBook === true && $result['id'] == $book->getId()) {
                continue;
            }
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Load collection from tag ID
     *
     * @param int         $tagId
     *
     * @return Collection
     *
     */
    public function getByTagId($tagId)
    {
        $resource = $this->getResource();

        foreach($resource->loadByTagId($tagId) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Load collection by keyword
     *
     * @param int             $tagId
     *
     * @return Collection
     */
    public function getByKeyword($keyword)
    {
        $resource = $this->getResource();

        foreach($resource->loadByKeyword($keyword) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Load book files information for a book collection
     *
     * @param  \Cops\Model\Book\Collection $bookCollection
     *
     * @return Collection
     */
    public function addBookFiles()
    {
        return $this->getResource()->getModel('BookFile')->populateBookCollection($this);
    }

}
