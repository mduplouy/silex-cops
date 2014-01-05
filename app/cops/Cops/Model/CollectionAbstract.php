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
    private $_elements = array();

    /**
     * Object entity model instance
     * @var Cops\Model\Common
     */
    private $_entity;

    /**
     * Object model resource instance
     * @var Cops\Model\Resource
     */
    private $_resource;

    /**
     * Constructor
     *
     * @param Common $entity Related entity instance for collection
     */
    public function __construct(Common $entity)
    {
        $this->_entity = $entity;
        $this->_resource = $entity->getResource();
    }

    /**
     * Entity model getter
     *
     * @return \Cops\Model\Common
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Resource getter
     *
     * @return \Cops\Model\Resource
     */
    public function getResource()
    {
        return $this->_resource;
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
        return new \ArrayIterator($this->_elements);
    }

    /**
     * Add an element into collection
     *
     * @param mixed $element
     *
     * @return Cops\Model\Collection
     */
    public function add($element)
    {
        $this->_elements[$element->getId()] = $element;
        return $this;
    }

    /**
     * Get element by ID
     *
     * @param int $id
     */
    public function getById($id)
    {
        return $this->_elements[$id];
    }

    /**
     * Count elements
     * Implements \Countable
     * @return int
     */
    public function count()
    {
        return count($this->_elements);
    }
}