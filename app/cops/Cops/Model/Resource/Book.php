<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Resource;

use Cops\Model\Resource;
use Cops\Model\Core;

/**
 * Book resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Book extends Resource
{
    /**
     * Load a book data
     *
     * @param int $bookId
     *
     * @return array();
     */
    public function load($bookId)
    {
        $sql = 'SELECT
            *
            FROM books AS main
            WHERE main.id = ?';

        $output = $this->getConnection()
            ->fetchAssoc(
                $sql,
                array(
                    (int) $bookId
                )
            );

        if (empty($output)) {
            throw new \Exception('Product not found');
        }
        return $output;
    }

    /**
     * Load latest added books from database
     *
     * @return array();
     */
    public function getLatest(\Cops\Model\Book $bookObj)
    {
        $sql = 'SELECT
            main.id,
            main.title,
            strftime("%Y", main.pubdate) AS year,
            main.series_index AS serie_index,
            main.author_sort AS author,
            main.has_cover,
            comments.text AS comment,

            series.name AS serie_name,
            ratings.rating

            FROM books AS main
            LEFT OUTER JOIN comments ON comments.book = main.id
            LEFT OUTER JOIN books_series_link ON books_series_link.book = main.id
            LEFT OUTER JOIN series ON series.id = books_series_link.series
            LEFT OUTER JOIN books_ratings_link ON books_ratings_link.book = main.id
            LEFT OUTER JOIN ratings ON ratings.id = books_ratings_link.rating
            ORDER BY main.timestamp DESC
            LIMIT ?';

        return $this->getConnection()
            ->fetchAll($sql,
                array(
                    Core::getConfig()->getValue('last_added')
                )
            );

    }
}
