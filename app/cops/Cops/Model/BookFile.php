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
        return $this->getResource()->getCollectionBySerieId(
            $serieId,
            $this,
            $this->getCollection()
        );
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
        return $this->getResource()->getCollectionByAuthorId(
            $authorId,
            $this,
            $this->getCollection()
        );
    }
}
