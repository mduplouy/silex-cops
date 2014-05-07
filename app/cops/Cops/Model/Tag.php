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

/**
 * Tag model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Tag extends EntityAbstract
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * Number of books
     * @var int
     */
    protected $bookCount;

    /**
     * Load Tag
     *
     * @param int $tagId
     *
     * @return Tag
     */
    public function load($tagId)
    {
        return $this->setData(
            $this->getResource()->load($tagId)
        );
    }

    /**
     * Load tag by name
     *
     * @param string  $name
     *
     * @return Tag
     */
    public function loadByName($name)
    {
        $data = $this->getResource()->loadByName($name);

        if (!empty($data)) {
            $this->setData($data);
        }

        return $this;
    }

    /**
     * Get number of books associated to a tag
     *
     * @return int
     */
    public function getNumberOfBooks()
    {
        if (is_null($this->bookCount)) {
            $this->bookCount = $this->getResource()->countBooks($this->getId());
        }
        return $this->bookCount;
    }

    /**
     * Get all books associated to a tag
     *
     * @param int      $firstResult Start offset
     * @param int|null $maxResults  Number of books to return
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getAllBooks($firstResult=0, $maxResults=null)
    {
        $collection = $this->app['model.book']->getCollection()
            ->setFirstResult($firstResult);

        if ($maxResults !== null) {
            $collection->setMaxResults($maxResults);
        }

        return $collection->getByTagId($this->getId());
    }

    /**
     * Delete all tags associated to given book id
     *
     * @param  int $bookId
     *
     * @return bool
     */
    public function deleteFromBook($bookId)
    {
        return (bool) $this->getResource()->deleteByBookId((int) $bookId);
    }

    /**
     * Associate tag to given book ID
     *
     * @param  int    $bookId
     * @param  string $name
     *
     * @return bool
     */
    public function associateToBook($bookId, $name = null)
    {
        if (is_null($name)) {
            $name = $this->getName();
        }

        $resource = $this->getResource();

        $resource->setEntity($this);

        if (is_null($this->getId())) {
            $this->setId($resource->insert($name));
        }

        return (bool) $resource->linkToBookId((int) $bookId);
    }

    /**
     * Empty properties on clone
     */
    public function __clone()
    {
        parent::__clone();
        $this->id = null;
        $this->name = null;
    }
}