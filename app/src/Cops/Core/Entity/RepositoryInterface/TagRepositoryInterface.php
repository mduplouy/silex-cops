<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\RepositoryInterface;

use Cops\Core\RepositoryInterface;
use Cops\Core\Entity\Book;
use Cops\Core\Entity\Tag;
use Cops\Core\Entity\BookCollection;

/**
 * Tag repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface TagRepositoryInterface extends RepositoryInterface
{
    /**
     * Load a tag data
     *
     * @param  int   $tagId
     *
     * @return array
     */
    public function findById($tagId);

    /**
     * Load tag by name
     *
     * @param  string $name
     *
     * @return array
     */
     public function findByName($name);

    /**
     * Count book number linked to tag
     *
     * @param  int $tagId
     *
     * @return int
     */
    public function countBooks($tagId);

    /**
     * Load all tags and count linked books
     *
     * @return array
     */
    public function loadAllWithBookCount();

    /**
     * Load all tag names
     *
     * @return array
     */
    public function loadAllNames();

    /**
     * Find by book ID
     *
     * @param  int   $bookId
     *
     * @return array
     */
    public function findByBookId($bookId);

    /**
     * Delete a tag
     *
     * @param  int $tagId
     *
     * @return int
     */
    public function delete($tagId);

    /**
     * Remove all tags associated to bookId
     *
     * @param  Book $book
     *
     * @return bool
     */
    public function deleteFromBook(Book $book);

    /**
     * Insert new name to database
     *
     * @param  Tag $tag
     *
     * @return int Inserted ID
     */
    public function insert(Tag $tag);

    /**
     * Associate tag to book
     *
     * @param  Tag  $tag
     * @param  Book $book
     *
     * @return int  Updated or inserted relation ID
     */
    public function associateToBook(Tag $tag, Book $book);

    /**
     * Count all tags
     *
     * @return int
     */
    public function countAll();

    /**
     * Load tag from a book collection
     *
     * @param  BookCollection $books
     *
     * @return array
     */
    public function loadFromBooks(BookCollection $books);
}
