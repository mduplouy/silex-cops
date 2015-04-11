<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Core\Entity;

use Cops\Core\AbstractCollection;

/**
 * User collection
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserCollection extends AbstractCollection
{
    /**
     * Find all
     *
     * @return $this
     */
    public function findAll()
    {
        return $this->setDataFromArray(
            $this->getRepository()->findAll()
        );
    }
}
