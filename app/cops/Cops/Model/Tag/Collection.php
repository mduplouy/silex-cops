<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Model\Tag;

use Cops\Model\CollectionAbstract;

/**
 * Tag collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Collection extends CollectionAbstract
{
    /**
     * Load all tags along with book count
     *
     * @return Collection
     */
    public function getAllWithBookCount()
    {
        $resource = $this->getResource();

        foreach ($resource->loadAllWithBookCount() as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Load all tag names
     *
     * @return Collection
     */
    public function getAllNames()
    {
        $resource = $this->getResource();

        foreach ($resource->loadAllNames() as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }

    /**
     * Load tag collection from book id
     *
     * @param int $bookId
     *
     * @return Collection
     */
    public function getByBookId($bookId)
    {
        $resource = $this->getResource();

        foreach ($resource->loadByBookId($bookId) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }
}