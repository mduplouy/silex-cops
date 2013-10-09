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

use Cops\Model\BookFileAbstract;
use Cops\Model\Book\Collection;

/**
 * Book file abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookFile extends BookFileAbstract
{
    /**
     * Get book files by serie ID
     *
     * @param int $serieId
     *
     * @return \Cops\Model\BookFile\Collection
     */
    public function getCollectionBySerieId($serieId)
    {
        return $this->getResource()->getCollectionBySerieId($serieId, $this);
    }

    /**
     * Get book files by author ID
     *
     * @param int $authorId
     *
     * @return \Cops\Model\BookFile\Collection
     */
    public function getCollectionByAuthorId($authorId)
    {
        return $this->getResource()->getCollectionByAuthorId($authorId, $this);
    }

    /**
     * Add book files to a book collection
     *
     * @param  \Cops\Model\Book\Collection $collection
     *
     * @return \Cops\Model\Book\Collection
     */
    public function populateBookCollection(Collection $collection)
    {
        return $this->getResource()->populateBookCollection($collection, $this);
    }

    /**
     * Reset data on cloning
     */
    public function __clone()
    {
        $this->format = null;
        $this->uncompressedSize = 0;
        $this->name = null;
        $this->directory = null;
    }
}
