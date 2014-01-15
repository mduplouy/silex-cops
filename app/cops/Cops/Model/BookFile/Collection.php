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

/**
 * BookFile collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Collection extends CollectionAbstract implements \IteratorAggregate, \Countable
{
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
}
