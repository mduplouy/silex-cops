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

/**
 * Book repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface BookRepositoryInterface extends RepositoryInterface
{
    /**
     * Find by id
     *
     * @param  mixed            $bookId
     *
     * @return array
     */
    public function findById($bookId);

    /**
     * Find books sorted by date
     *
     * @return array
     */
    public function findSortedByDate();

    /**
     * Find all books
     *
     * @return array
     */
    public function findAll();

    /**
     * Count all books
     *
     * @return int
     */
    public function countAll();

    /**
     * Find by serie id
     *
     * @param  int   $serieId
     *
     * @return array
     */
    public function findBySerieId($serieId);

    /**
     * Find by author id
     *
     * @param int    $authorId
     *
     * @return array
     */
    public function findByAuthorId($authorId);

    /**
     * Find by tag id
     *
     * @param int    $tagId
     *
     * @return array
     */
    public function findByTagId($tagId);

    /**
     * Find by keyword
     *
     * @param  array  $keywords
     *
     * @return array
     */
    public function findByKeyword(array $keywords);

    /**
     * Define excluded book id
     *
     * @param  int   $id
     *
     * @return $this
     */
    public function setExcludedBookId($id);

    /**
     * Define excluded serie id
     *
     * @param  int   $id
     *
     * @return $this
     */
    public function setExcludedSerieId($id);
}
