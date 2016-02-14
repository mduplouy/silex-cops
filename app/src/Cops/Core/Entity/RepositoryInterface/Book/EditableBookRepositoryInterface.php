<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\RepositoryInterface\Book;

use Cops\Core\Entity\RepositoryInterface\BookRepositoryInterface;

/**
 * Editable Book repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface EditableBookRepositoryInterface extends BookRepositoryInterface
{
    /**
     * Update author & author_sort
     *
     * @param int   $bookId
     * @param array $authors
     *
     * @return bool
     */
    public function updateAuthor($bookId, $authors);

    /**
     * Update title & title sort
     *
     * @param int    $bookId
     * @param string $title
     *
     * @return bool
     */
    public function updateTitle($bookId, $title);

    /**
     * Update publication date
     *
     * @param int       $bookId
     * @param \DateTime $pubDate
     *
     * @return bool
     */
    public function updatePubDate($bookId, \DateTime $pubDate);

    /**
     * Update comment
     *
     * @param int       $bookId
     * @param string    $comment
     *
     * @return bool
     */
    public function updateComment($bookId, $comment);

    /**
     * Get book language code from DB
     *
     * @param  int    $bookId
     *
     * @return string
     */
    public function getBookLanguageCode($bookId);
}
