<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Model\User;

use Cops\Model\CollectionAbstract;

/**
 * User collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Collection extends CollectionAbstract
{
    /**
     * Load all users
     *
     * @return Collection
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
     * Load user collection by role
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
