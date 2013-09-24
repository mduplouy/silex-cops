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

abstract class Collection implements \IteratorAggregate, \Countable
{
    /**
     * Collection elements
     * @var array
     */
    private $_elements = array();

    /**
     * Object resource
     * @var \Cops\Model\Resource
     */
    private $_resource;

    /**
     * Constructor
     *
     * @param \Cops\Model\Common $obj Related instance for collection
     *
     */
    public function __construct(\Cops\Model\Common $obj)
    {
        $this->_resource = $obj->getResource();
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
     */
    public function add($element)
    {
        $this->_elements[] = $element;
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