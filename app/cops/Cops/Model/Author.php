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
 * Author model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Author extends EntityAbstract
{
    /**
     * Object ID
     * @var int
     */
    protected $id;

    /**
     * Author name
     * @var string
     */
    protected $name;

    /**
     * Sort name
     * @var string
     */
    protected $sort;

    /**
     * Number of books
     * @var int
     */
    protected $bookCount;

    /**
     * Load author data
     *
     * @param int $authorId
     *
     * @return \Cops\Model\Author
     */
    public function load($authorId)
    {
        return $this->setData(
            $this->getResource()->load($authorId)
        );
    }

    /**
     * Get sort name using Calibre algorithm
     *
     * @return string|null
     */
    public function getSort()
    {
        if ($this->sort === null && $this->name !== null) {
            $this->sort = $this->app['model.calibre']->getAuthorSortName($this->name);
        }
        return $this->sort;
    }

    /**
     * Get the latest added books
     *
     * @return array Array of Book object
     */
    public function getAggregatedList()
    {
        $output = array();
        foreach ($this->getResource()->getAggregatedList($this) as $author) {
            // Force non alpha to #
            if (!preg_match('/[A-Z]/', $author['first_letter'])) {
                $author['first_letter'] = '#';
            }
            if (!array_key_exists($author['first_letter'], $output)) {
                $output[$author['first_letter']] = 0;
            }

            $output[$author['first_letter']] += $author['nb_author'];
        }
        return $output;
    }

    /**
     * Get number of books of author
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
     * Get all books from author
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getAllBooks()
    {
        return $this->app['model.book']->getCollection()
            ->getByAuthorId($this->getId())
            ->addBookFiles();
    }

    /**
     * Save author to DB
     *
     * @return int
     */
    public function save()
    {
        $resource = $this->getResource();

        if ($this->getId()) {
            $authorId = $this->getId();
            $resource->update();
        } else {
            $authorId = $resource->insert();
        }
        return $authorId;
    }

    /**
     * Delete author from DB
     *
     * @return bool
     */
    public function delete()
    {
        return $this->getResource()->delete();
    }

    /**
     * Reset data on clone
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();
        $this->id        = null;
        $this->name      = null;
        $this->sort      = null;
        $this->bookCount = 0;
    }
}
