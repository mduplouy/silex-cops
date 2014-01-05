<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Book;

use Cops\Model\Resource as BaseResource;
use Cops\Exception\BookException;
use Cops\Model\Core;
use Cops\Model\Book\Collection;
use PDO;

/**
 * Book resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends BaseResource
{
    protected $_baseSelect = 'SELECT
        main.*,
        comments.text AS comment,
        ratings.rating,

        authors.id   AS author_id,
        authors.name AS author_name,
        authors.sort AS author_sort,

        series.id   AS serie_id,
        series.name AS serie_name,
        series.sort AS serie_sort

        FROM books AS main
        LEFT OUTER JOIN comments ON comments.book = main.id
        LEFT OUTER JOIN books_authors_link ON books_authors_link.book = main.id
        LEFT JOIN authors ON authors.id = books_authors_link.author
        LEFT OUTER JOIN books_series_link ON books_series_link.book = main.id
        LEFT OUTER JOIN series ON series.id = books_series_link.series
        LEFT OUTER JOIN books_ratings_link ON books_ratings_link.book = main.id
        LEFT OUTER JOIN ratings ON ratings.id = books_ratings_link.rating';

    /**
     * Load a book data
     *
     * @param  int              $bookId
     * @param  \Cops\Model\Book $book
     *
     * @return array;
     */
    public function load($bookId)
    {
        /**
         * Load book common informations
         */
        $sql = $this->getBaseSelect() . '
            WHERE
            main.id = ?';

        $result = $this->getConnection()->fetchAssoc(
            $sql,
            array(
                (int) $bookId,
            )
        );

        if (empty($result)) {
            throw new BookException(sprintf('Product width id %s not found', $bookId));
        }
        return $result;
    }

    /**
     * Load latest added books from database
     *
     * @param  int          $nb  Number of items to load
     *
     * @return PDOStatement
     */
    public function loadLatest($nb)
    {
        $sql = $this->getBaseSelect(). '
            ORDER BY main.timestamp DESC
            LIMIT :limit';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':limit', (int) $nb);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Load books by serie ID
     *
     * @param  int          $serieId
     *
     * @return PDOStatement
     */
    public function loadBySerieId($serieId)
    {
        $sql = $this->getBaseSelect(). '
            WHERE
            series.id = :serie_id
            ORDER BY serie_name, series_index, title
            LIMIT :limit';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':serie_id', (int) $serieId);
        $stmt->bindValue(':limit', 25);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Load books by author ID
     *
     * @param int              $authorId
     * @param bool             $addFiles
     *
     * @return PDOStatement
     */
    public function loadByAuthorId($authorId)
    {
        $sql = $this->getBaseSelect(). '
            WHERE
            authors.id = :author_id
            ORDER BY serie_name, series_index, title';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':author_id', $authorId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Load books collection by tag ID
     *
     * @param int              $tagId
     *
     * @return PDOStatement
     */
    public function loadByTagId($tagId)
    {
        $sql = $this->getBaseSelect(). '
            INNER JOIN books_tags_link ON (
                main.id = books_tags_link.book
            )
            INNER JOIN tags ON ( tags.id = books_tags_link.tag)
            WHERE
            tags.id = :tag_id
            ORDER BY serie_name, series_index, title';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':tag_id', (int) $tagId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Load collection based on keyword
     *
     * @param  str          $keyword
     *
     * @return PDOStatement
     */
    public function loadByKeyword($keyword) {

        $sql = $this->getBaseSelect(). '
            LEFT OUTER JOIN books_tags_link ON (
                main.id = books_tags_link.book
            )
            LEFT OUTER JOIN tags ON ( tags.id = books_tags_link.tag)
            WHERE
            main.path LIKE :search
            ORDER BY serie_name, series_index, title';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':search', '%'.$keyword.'%');
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Set data from statement
     *
     * @param array            $result The result array from select stmt
     *
     * @return \Cops\Model\Book;
     */
    public function setDataFromStatement(array $result)
    {
        $myBook = parent::setDataFromStatement($result);

        $myBook->getAuthor()->setData(array(
            'id'   => $result['author_id'],
            'name' => $result['author_name'],
            'sort' => $result['author_sort'],
        ));

        if (!empty($result['serie_id'])) {
            $myBook->getSerie()->setData(array(
                'id'   => $result['serie_id'],
                'name' => $result['serie_name'],
                'sort' => $result['serie_sort'],
            ));
        }

        return $myBook;
    }
}
