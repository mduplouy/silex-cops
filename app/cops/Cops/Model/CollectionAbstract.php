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

use Cops\Model\Common;

/**
 * Collection abstract class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class CollectionAbstract implements \IteratorAggregate, \Countable
{
    /**
     * Collection elements
     * @var array
     */
    private $elements = array();

    /**
     * Object entity model instance
     * @var Common
     */
    private $entity;

    /**
     * Object model resource instance
     * @var Resource
     */
    private $resource;

    /**
     * Constructor
     *
     * @param Common $entity Related entity instance for collection
     */
    public function __construct(Common $entity)
    {
        $this->entity = $entity;
        $this->resource = $entity->getResource();
    }

    /**
     * Entity model getter
     *
     * @return Common
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Resource getter
     *
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Gets the elements as an Iterator.
     *
     * Implements \IteratorAggregate.
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over collection elements
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * Add an element into collection
     *
     * @param mixed $element
     *
     * @return Collection
     */
    public function add($element)
    {
        $this->elements[$element->getId()] = $element;
        return $this;
    }

    /**
     * Get element by ID
     *
     * @param int $id
     */
    public function getById($id)
    {
        return $this->elements[$id];
    }

    /**
     * Count elements
     * Implements \Countable
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Set start offset for "sliced" collection
     *
     * @param int $offset
     *
     * @return Collection
     */
    public function setFirstResult($offset)
    {
        $this->getResource()->setFirstResult($offset);
        return $this;
    }

    /**
     * Set desired size for "sliced" collection
     *
     * @param int $limit
     *
     * @return Collection
     */
    public function setMaxResults($limit)
    {
        $this->getResource()->setMaxResults($limit);
        return $this;
    }
}