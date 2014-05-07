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

use Cops\Model\EntityAbstract;
use Silex\Application as BaseApplication;

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
    protected $elements = array();

    /**
     * Id => Key mapping for elements
     * @var array
     */
    protected $mapping = array();

    /**
     * Object entity model instance
     * @var Common
     */
    private $entity;

    /**
     * Constructor
     *
     * @param Common $entity Related entity instance for collection
     */
    public function __construct(EntityAbstract $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Entity model getter
     *
     * @return \Cops\Model\EntityAbstract
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * App getter
     *
     * @return \Silex\Application
     */
    public function getApp()
    {
        return $this->getEntity()->getApp();
    }

    /**
     * Get all elements id
     *
     * @return array
     */
    public function getAllIds()
    {
        return array_keys($this->mapping);
    }

    /**
     * Resource getter
     *
     * @return \Cops\Model\ResourceAbstract
     */
    public function getResource()
    {
        return $this->getEntity()->getResource();
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
     * @return $this
     */
    public function add($element)
    {
        $this->elements[] = $element;
        $this->mapping[$element->getId()] = count($this->elements) - 1;
        return $this;
    }

    /**
     * Get element by ID
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return $this->elements[$this->mapping[$id]];
    }

    /**
     * Get element by key
     *
     * @param int $key
     *
     * @return mixed
     */
    public function getByKey($key)
    {
        return $this->elements[$key];
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
     * @return $this
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
     * @return $this
     */
    public function setMaxResults($limit)
    {
        $this->getResource()->setMaxResults($limit);
        return $this;
    }
}
