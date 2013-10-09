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

use Cops\Model\Exception\BookException;
use Cops\Model\Core;
use \PDO;

/**
 * Book resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends \Cops\Model\Resource
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
        LEFT OUTER JOIN authors ON authors.id = books_authors_link.author
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
     * @return \Cops\Model\Book;
     */
    public function load($bookId, \Cops\Model\Book $book)
    {
        /**
         * Load book common informations
         */
        $sql = $this->getBaseSelect() . '
            WHERE
            main.id = ?';

        $result = $this->getConnection()
            ->fetchAssoc(
                $sql,
                array(
                    (int) $bookId,
                )
            );

        if (empty($result)) {
            throw new BookException(sprintf(
                'Product width id %s not found',
                $bookId
            ));
        }

        $this->_setDataAfterSelect($book, $result);

        /**
         * Load book file informations
         */
        $bookFiles = $this->getConnection()->fetchAll('SELECT
            book,
            format,
            uncompressed_size,
            name
            FROM data
            WHERE book = ?',
            array((int) $bookId)
        );

        foreach($bookFiles as $bookFile) {
            $file = $book->getFile($bookFile['format']);
            $file->setData($bookFile);
            $file->setDirectory($book->getPath());
        }

        return $book;
    }

    /**
     * Load latest added books from database
     *
     * @param \Cops\Model\Book       $book
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getLatestCollection(\Cops\Model\Book $book)
    {
        $sql = $this->getBaseSelect(). '
            ORDER BY main.timestamp DESC
            LIMIT :limit';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':limit', Core::getConfig()->getValue('last_added'));
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $collection = $book->getCollection();

        foreach($stmt as $result) {
            $myBook = clone($book);
            $collection->add($this->_setDataAfterSelect($myBook, $result));
        }
        return $collection;
    }

    /**
     * Get other books from author
     *
     * @param int              $authorId
     * @param \Cops\Model\Book $book
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getOtherBooksFromAuthor($authorId, \Cops\Model\Book $book)
    {
        $sql = $this->getBaseSelect(). '
            WHERE
            authors.id = :author_id
            AND main.id != :book_id';

        $serieId = $book->getSerie()->getId();
        if (!is_null($serieId)) {
            $sql .= ' AND series.id != :serie_id';
        }

        $sql .= ' ORDER BY serie_name, series_index, title';
        $sql .= ' LIMIT :limit';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':author_id', $authorId);
        $stmt->bindValue(':book_id', $book->getId());
        if (!is_null($serieId)) {
            $stmt->bindValue(':serie_id', $serieId);
        }
        $stmt->bindValue(':limit', 25);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $collection = $book->getCollection();

        foreach($stmt as $result) {
            $myBook = clone($book);
            $this->_setDataAfterSelect($myBook, $result);

            $collection->add($myBook);
        }

        return $collection;
    }

    /**
     * Get other books from serie
     *
     * @param int              $serieId
     * @param \Cops\Model\Book $book
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getOtherBooksFromSerie($serieId, \Cops\Model\Book $book)
    {
        $sql = $this->getBaseSelect(). '
            WHERE
            series.id = :serie_id
            AND main.id != :book_id
            ORDER BY serie_name, series_index, title
            LIMIT :limit';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':book_id', $book->getId());
        $stmt->bindValue(':serie_id', $serieId);
        $stmt->bindValue(':limit', 25);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $collection = $book->getCollection();

        foreach($stmt as $result) {
            $myBook = clone($book);
            $this->_setDataAfterSelect($myBook, $result);

            $collection->add($myBook);
        }

        return $collection;
    }

    /**
     * Load books by serie ID
     *
     * @param int              $serieId
     * @param \Cops\Model\Book $book
     *
     * @return \Cops\Model\Book\Collection
     */
    public function loadBySerieId($serieId, \Cops\Model\Book $book, $addFiles=true)
    {
        $sql = $this->getBaseSelect(). '
            WHERE
            series.id = :serie_id
            ORDER BY serie_name, series_index, title';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':serie_id', $serieId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $collection = $book->getCollection();

        foreach($stmt as $result) {
            $myBook = clone($book);
            $this->_setDataAfterSelect($myBook, $result);

            $collection->add($myBook);
        }

        // Load book files information
        if ($addFiles === true) {
            $this->_loadBookFiles($collection);
        }

        return $collection;
    }

    /**
     * Load books by author ID
     *
     * @param int              $authorId
     * @param \Cops\Model\Book $book
     * @param bool             $addFiles
     *
     * @return \Cops\Model\Book\Collection
     */
    public function loadByAuthorId($authorId, \Cops\Model\Book $book, $addFiles=true)
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

        $collection = $book->getCollection();

        foreach($stmt as $result) {
            $myBook = clone($book);
            $this->_setDataAfterSelect($myBook, $result);
            $collection->add($myBook);
        }

        // Load book files information
        if ($addFiles === true) {
            $this->_loadBookFiles($collection);
        }

        return $collection;
    }

    /**
     * Set data after select statement
     *
     * @param \Cops\Model\Book $book   The book instance
     * @param array            $result The result array from select stmt
     *
     * @return \Cops\Model\Book;
     */
    private function _setDataAfterSelect(\Cops\Model\Book $book, array $result)
    {
        // Set serie object
        $book->getSerie()->setData(array(
            'id'   => $result['serie_id'],
            'name' => $result['serie_name'],
            'sort' => $result['serie_sort'],
        ));
        $book->getAuthor()->setData(array(
            'id'   => $result['author_id'],
            'name' => $result['author_name'],
            'sort' => $result['author_sort'],
        ));
        return $book->setData($result);
    }

    /**
     * Load book files information
     *
     * @param  \Cops\Model\Book\Collection $bookCollection
     *
     * @return \Cops\Model\Book\Collection
     */
    private function _loadBookFiles($bookCollection)
    {
        $app = Core::getApp();
        return $app['core']->getModel('BookFile')->populateBookCollection($bookCollection);
    }
}
