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
 * Collection abstract class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractCollection extends AbstractEntity implements \IteratorAggregate, \Countable
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
     * Repository instance
     * @var RepositoryInterface
     */
    protected $repository;

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
     * Set data from array
     *
     * @param array $elementsData
     *
     * @return self
     */
    public function setDataFromArray(array $elementsData)
    {
        $entityKey = str_replace('collection', 'entity', $this->getServiceKey());

        foreach ($elementsData as $elementData) {
            $this->add($this->app[$entityKey]->setDataFromArray($elementData));
        }

        return $this;
    }

    /**
     * Add an element into collection
     *
     * @param CollectionableInterface $element
     *
     * @return $this
     */
    public function add(CollectionableInterface $element)
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
     * @return CollectionableInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getById($id)
    {
        if (!array_key_exists($id, $this->mapping)) {
            throw new \InvalidArgumentException(sprintf(
                'Element with ID %s does not exists',
                $id
            ));
        }

        return $this->getByKey($this->mapping[$id]);
    }

    /**
     * Get element by key
     *
     * @param int $key
     *
     * @return CollectionableInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getByKey($key)
    {
        if (!array_key_exists($key, $this->elements)) {
            throw new \InvalidArgumentException(sprintf(
                'Element with KEY %s does not exists',
                $key
            ));
        }

        return $this->elements[$key];
    }

    /**
     * Count elements
     * Implements \Countable
     *
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
     * @return self
     */
    public function setFirstResult($offset)
    {
        $this->getRepository()->setFirstResult((int) $offset);

        return $this;
    }

    /**
     * Set desired size for "sliced" collection
     *
     * @param int $limit
     *
     * @return self
     */
    public function setMaxResults($limit)
    {
        $this->getRepository()->setMaxResults((int) $limit);

        return $this;
    }
}
