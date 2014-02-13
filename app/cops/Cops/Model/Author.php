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

/**
 * Author model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Author extends Common
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
        return $this->getModel('Book')->getCollection()
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
     * Reset data on clone
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();
        $this->id   = null;
        $this->name = null;
        $this->sort = null;
    }
}
