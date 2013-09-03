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

use PDO;

/**
 * Book resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Book extends \Cops\Model\Resource
{
    /**
     * Table name
     * @var string
     */
    private $_tableName = 'books';

    /**
     * Load latest added books from database
     *
     * @return array();
     */
    public function getLatest(\Cops\Model\Book $bookObj)
    {
        $db = $this->getConnection();

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
            LIMIT 10';

        return $db->fetchAll($sql);
    }
}
