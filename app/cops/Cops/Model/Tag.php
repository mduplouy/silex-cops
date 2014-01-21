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
 * Tag model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Tag extends Common
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
     * @return Collection
     */
    public function getAllBooks($firstResult=0, $maxResults=null)
    {
        $collection = $this->getModel('Book')->getCollection()
            ->setFirstResult($firstResult);

        if ($maxResults !== null) {
            $collection->setMaxResults($maxResults);
        }

        return $collection->getByTagId($this->getId());
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