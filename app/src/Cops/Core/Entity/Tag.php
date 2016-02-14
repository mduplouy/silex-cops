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

use Cops\Core\AbstractBookCount;
use Cops\Core\CollectionableInterface;
use Cops\Core\Entity\Exception\TagNotFoundException;

/**
 * Tag entity
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Tag extends AbstractBookCount implements CollectionableInterface
{
    /**
     * Repository interface to be checked
     */
    const REPOSITORY_INTERFACE = 'Cops\Core\Entity\RepositoryInterface\TagRepositoryInterface';

    /**
     * Book ID for book linking
     * @var int
     */
    private $bookId;

    /**
     * Name
     * @var string
     */
    private $name;

    /**
     * Set bookId
     *
     * @param  int   $bookId
     *
     * @return $this
     */
    public function setBookId($bookId)
    {
        $this->bookId = (int) $bookId;

        return $this;
    }

    /**
     * Get bookId
     *
     * @return int
     */
    public function getBookId()
    {
        return $this->bookId;
    }

    /**
     * Set name
     *
     * @param  string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Find by id
     *
     * @param  int   $tagId
     *
     * @return $this
     */
    public function findById($tagId)
    {
        $result = $this->setId($tagId)
            ->getRepository()
            ->findById($tagId);

        if (empty($result)) {
            throw new TagNotFoundException(sprintf('Tag width id %s not found', $tagId));
        }

        return $this->setDataFromArray($result);
    }

    /**
     * Find by name
     *
     * @param  string $tagName
     *
     * @return $this
     */
    public function findByName($tagName)
    {
        $result = $this->setName($tagName)
            ->getRepository()
            ->findByName($tagName);

        if (empty($result)) {
            throw new TagNotFoundException(sprintf('Tag width name %s not found', $tagName));
        }

        return $this->setDataFromArray($result);
    }

    /**
     * Delete all tags associated to given book id
     *
     * @param  Book $book
     *
     * @return bool
     *
     * @deprecated to be moved into collection
     */
    public function deleteFromBook(Book $book)
    {
        return (bool) $this->getRepository()
            ->deleteFromBook($book);
    }

    /**
     * Associate tag to given book ID
     *
     * @param  Book   $book
     *
     * @return bool
     */
    public function associateToBook(Book $book)
    {
        if (!$this->getId()) {
            try {
                $this->findByName($this->getName());
            } catch (TagNotFoundException $e) {
                // Do noting it's a new tag
            }
        }

        return (bool) $this->getRepository()
            ->associateToBook($this, $book);
    }
}
