<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Model\Serie;

use Cops\Model\CollectionAbstract;

/**
 * Serie collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Collection extends CollectionAbstract
{
    /**
     * Get collection based on first letter
     *
     * @param str $letter
     *
     * @return Collection
     */
    public function getByFirstLetter($letter)
    {
        $resource = $this->getResource();

        foreach ($resource->loadByFirstLetter($letter) as $result) {
            $this->add($resource->setDataFromStatement($result));
        }
        return $this;
    }
}
