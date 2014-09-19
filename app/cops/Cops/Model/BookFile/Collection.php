<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Model\BookFile;

use Cops\Model\CollectionAbstract;
use Cops\Model\Book;

use Cops\Exception\BookFile\FormatUnavailableException;

/**
 * BookFile collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Collection extends CollectionAbstract
{
    /**
     * Format mapping
     * @var array
     */
    private $formatMapping = array();

    /**
     * Add an element into collection
     *
     * @param mixed $element
     *
     * @return Collection
     */
    public function add($element)
    {
        $this->elements[] = $element;
        $this->mapping[$element->getBookId()] = count($this->elements) - 1;
        $this->formatMapping[$element->getBookId()][$element->getFormat()] = count($this->elements) - 1;
        return $this;
    }

    /**
     * Get a bookfile from collection by specifying format
     *
     * @param  string $format
     * @param  int    $bookId
     *
     * @return \Cops\Model\BookFile\AdapterAbstract
     *
     * @throws FormatUnavailableException
     */
    public function findFormat($format, $bookId)
    {
        if (isset($this->formatMapping[$bookId][$format])) {
            $collectionKey = $this->formatMapping[$bookId][$format];
            return $this->elements[$collectionKey];
        }

        throw new FormatUnavailableException(
            sprintf('Could not get %s format for book id %s', $format, $bookId)
        );
    }

    /**
     * Load book files collection based on serie ID
     *
     * @param int $serieId
     *
     * @return Collection
     */
    public function getBySerieId($serieId)
    {
        $resource = $this->getResource();

        foreach ($resource->loadBySerieId($serieId) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }

        return $this;
    }

    /**
     * Load book files collection based on author ID
     *
     * @param int $authorId
     *
     * @return Collection
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
     * Load book files from single book
     *
     * @param \Cops\Model\Book
     *
     * @return Collection
     */
    public function getFromBook(Book $book)
    {
        $resource = $this->getResource();

        foreach ($resource->loadByBookId($book->getId()) as $result) {
            $elm = $resource->setDataFromStatement($result);
            $elm->setDirectory($book->getPath());
            $this->add($elm);
        }
        return $this;
    }

    /**
     * Load book files collection based on tag ID
     *
     * @param int $tagId
     *
     * @return Collection
     */
    public function getByTagId($tagId)
    {
        $resource = $this->getResource();

        foreach ($resource->loadByTagId($tagId) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }

        return $this;
    }
}
