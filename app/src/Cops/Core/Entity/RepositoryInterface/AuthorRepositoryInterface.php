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
use Cops\Core\Entity\Author;
use Cops\Core\Entity\BookCollection;

/**
 * Author repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface AuthorRepositoryInterface extends RepositoryInterface
{
    /**
     * Find by id
     *
     * @param  int    $authorId
     *
     * @return array
     */
    public function findById($authorId);

    /**
     * Save author
     *
     * @param Author $author
     *
     * @return int
     */
    public function save(Author $author);

    /**
     * Update author
     *
     * @param  Author $author
     *
     * @return int    Number of updated lines
     */
    public function update(Author $author);

    /**
     * Delete author
     *
     * @param  Author $author
     *
     * @return bool
     */
    public function delete(Author $author);

    /**
     * Count authors by first letter
     *
     * @return array
     */
    public function countGroupedByFirstLetter();

    /**
     * Count all authors
     *
     * @return int
     */
    public function countAll();

    /**
     * Count book number written by author
     *
     * @param  int $authorId
     *
     * @return int
     */
    public function countBooks($authorId);

    /**
     * Find by first letter
     *
     * @param string  $letter
     *
     * @return array
     */
    public function findByFirstLetter($letter);

    /**
     * Load by bookId
     *
     * @param  int   $bookId
     *
     * @return array
     */
    public function loadByBookId($bookId);

    /**
     * Load author data from a book collection
     *
     * @param  BookCollection $books
     *
     * @return array
     */
    public function loadFromBooks(BookCollection $books);
}
