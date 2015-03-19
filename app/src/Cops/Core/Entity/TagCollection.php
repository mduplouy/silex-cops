<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity;

use Cops\Core\AbstractCollection;

/**
 * Tag collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class TagCollection extends AbstractCollection
{
    /**
     * Load all tags along with book count
     *
     * @return TagCollection
     */
    public function findAllWithBookCount()
    {
        return $this->setDataFromArray(
            $this->getRepository()->loadAllWithBookCount()
        );
    }

    /**
     * Load all tag names
     *
     * @return TagCollection
     */
    public function findAll()
    {
        return $this->setDataFromArray(
            $this->getRepository()->loadAllNames()
        );
    }

    /**
     * Load tag collection from book id
     *
     * @param int $bookId
     *
     * @return Collection
     */
    public function findByBookId($bookId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByBookId($bookId)
        );
    }

    /**
     * Find from book collection
     *
     * @param BookCollection $books
     *
     * @return $this
     */
    public function findFromBooks(BookCollection $books)
    {
        if ($books->count()) {
            $this->setDataFromArray(
                $this->getRepository()->loadFromBooks($books)
            );
        }

        return $this;
    }

    /**
     * Count all authors
     *
     * @return int
     */
    public function countAll()
    {
        return $this->getRepository()->countAll();
    }
}
