<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core;

use Cops\Core\AbstractEntity;
use Cops\Core\CollectionableInterface;

/**
 * Abstract book count
 * @author Mathieu Duuplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractBookCount extends AbstractEntity implements CollectionableInterface, \JsonSerializable
{
    /**
     * Number of books
     * @var int
     */
    private $bookCount;

    /**
     * Id
     * @var mixed
     */
    private $id;

    /**
     * Set id
     *
     * @param int $id
     *
     * @return $this
     */
    final public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * Set bookCount
     *
     * @param  int $bookCount
     *
     * @return $this
     */
    final public function setBookCount($bookCount)
    {
        $this->bookCount = (int) $bookCount;

        return $this;
    }

    /**
     * Get number of books of author
     *
     * @return int
     */
    final public function getBookCount()
    {
        if ($this->bookCount === null) {
            $this->bookCount = $this->getRepository()->countBooks($this->getId());
        }

        return $this->bookCount;
    }

    /**
     * Json serialize
     *
     * @return array|null
     */
    public function jsonSerialize()
    {
        if (null === $this->id) {
            return null;
        }

        return array(
            'id'   => $this->id,
            'name' => $this->getName(),
        );
    }
}
