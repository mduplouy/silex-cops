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

use Cops\Model\CollectionAbstract;

/**
 * Book collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Collection extends CollectionAbstract
{
    /**
     * Get last added books
     *
     * @param int     $nb  Number of items to load
     *
     * @return $this
     */
    public function getLatest($nb)
    {
        $resource = $this->getResource()
            ->setMaxResults($nb);

        foreach ($resource->loadSortedByDate($nb) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }

        return $this->addAuthors();
    }

    /**
     * Get books sorted by date
     *
     * @param int $nb   Number of items to load
     * @param int $page Start offset
     *
     * @return $this
     */
    public function getSortedByDate()
    {
        $resource = $this->getResource();

        foreach ($resource->loadSortedByDate() as $result) {
            $this->add($resource->setDataFromStatement($result));
        }

        return $this->addBookFiles()
            ->addAuthors();
    }

    /**
     * Count all books
     *
     * @return int
     */
    public function countAll()
    {
        return (int) $this->getResource()->countAll();
    }

    /**
     * Get all books
     *
     * @return $this
     */
    public function getAll()
    {
        $resource = $this->getResource();

        foreach ($resource->loadAll() as $result) {
            $this->add($resource->setDataFromStatement($result));
        }

        return $this;
    }

    /**
     * Get book collection based on author
     * Remove current book from collection
     *
     * @return $this
     */
    public function getOtherBooksFromAuthor()
    {
        $book = $this->getEntity();

        $authorId = null;
        foreach($book->getAuthors() as $author) {
            $authorId = $author->getId();
            break;
        }

        $this->getResource()
            ->setExcludedBookId($book->getId())
            ->setExcludedSerieId($book->getSerie()->getId());

        return $this->getByAuthorId($authorId);
    }

    /**
     * Get other books from author
     *
     * @param  int    $authorId
     *
     * @return $this
     */
    public function getByAuthorId($authorId)
    {
        $resource = $this->getResource();

        foreach ($resource->loadByAuthorId($authorId) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Get book collection based on a serie
     * Remove current book from collection
     *
     * @return $this
     */
    public function getOtherBooksFromSerie()
    {
        $book = $this->getEntity();

        $this->getResource()
            ->setExcludedBookId($book->getId());

        return $this->getBySerieId($this->getEntity()->getSerie()->getId());
    }

    /**
     * Get book collection based on a serie
     *
     * @param int     $serieId
     *
     * @return $this
     */
    public function getBySerieId($serieId)
    {
        $resource = $this->getResource();

        foreach ($resource->loadBySerieId($serieId) as $result) {
;           $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Load collection from tag ID
     *
     * @param int     $tagId
     *
     * @return $this
     */
    public function getByTagId($tagId)
    {
        $resource = $this->getResource();

        foreach ($resource->loadByTagId($tagId) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }

        return $this->addAuthors()
            ->addBookFiles();
    }

    /**
     * Load collection by keyword
     *
     * @param  string      $keyword
     *
     * @return $this
     */
    public function getByKeyword($keyword)
    {
        $resource = $this->getResource();

        foreach ($resource->loadByKeyword($keyword) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Load book files information for a book collection
     *
     * @return $this
     */
    public function addBookFiles()
    {
        return $this->getApp()
            ->offsetGet('model.bookfile')
            ->populateBookCollection($this);
    }

    /**
     * Load and add author information to book collection
     *
     * @return $this
     */
    public function addAuthors()
    {
        return $this->getApp()
            ->offsetGet('model.author')
            ->getResource()
            ->populateBookCollection($this);
    }
}
