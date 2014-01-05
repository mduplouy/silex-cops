<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\BookFile;

use Cops\Model\Collection;
use Cops\Model\Book\Collection as BookCollection;
use Cops\Model\Resource as BaseResource;
use \PDO;

/**
 * BookFile resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends BaseResource
{
    protected $_baseSelect = 'SELECT
        main.id,
        main.book,
        main.format,
        main.uncompressed_size,
        main.name
        FROM data as main';

    /**
     * Get book files data by serie ID
     *
     * @param  int      $serieId
     *
     * @return PDOStatement
     */
    public function loadBySerieId($serieId)
    {
        $stmt = $this->_getCollectionByType('series', 'series', $serieId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get book files data by Author ID
     *
     * @param  int          $authorId
     *
     * @return PDOStatement
     */
    public function loadByAuthorId($authorId)
    {
        $stmt = $this->_getCollectionByType('authors', 'author', $authorId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Load bookfile data for a book collection
     *
     * @param  \Cops\Model\Book\Collection $collection
     *
     * @return \Cops\Model\Book\Collection
     */
    public function populateBookCollection(BookCollection $collection)
    {
        $bookIds = array();
        foreach($collection as $book) {
            $bookIds[] = $book->getId();
        }

        $sql = $this->getBaseSelect().'
            WHERE main.book IN (?)';

        $stmt = $this->getConnection()
            ->executeQuery(
                $sql,
                array($bookIds),
                array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
            );
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();

        foreach($stmt as $row) {
            $book = $collection->getById($row['book']);

            $bookFile = $book->getFile($row['format']);
            $bookFile->setDirectory($book->getPath());
            $bookFile->setData($row);
        }
        return $collection;
    }

    /**
     * Retrieve a collection of book file (for books/series ...)
     *
     * @param  string $tableName
     * @param  string $fieldName
     * @param  string $fieldValue
     *
     * @return PDOStatement
     */
    private function _getCollectionByType($tableName, $fieldName, $fieldValue)
    {
        $sql = 'SELECT
            data.id,
            data.format,
            data.uncompressed_size,
            data.name,
            books.path as directory
            FROM books_%s_link
            INNER JOIN data ON
                data.book = books_%s_link.book
            INNER JOIN books ON
                books.id = data.book
            WHERE books_%s_link.%s = ?';

        $sql = sprintf($sql,
            $tableName,
            $tableName,
            $tableName,
            $fieldName
        );

        return $this->getConnection()
            ->executeQuery($sql, array($fieldValue));
    }

}
