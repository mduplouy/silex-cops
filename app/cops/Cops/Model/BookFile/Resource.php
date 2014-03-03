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

use Cops\Model\ResourceAbstract;
use Cops\Model\Book\Collection as BookCollection;
use PDO;
use Doctrine\DBAL\Connection;

/**
 * BookFile resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends ResourceAbstract
{
    /**
     * Get book files data by serie ID
     *
     * @param  int      $serieId
     *
     * @return array
     */
    public function loadBySerieId($serieId)
    {
        return $this->getCollectionByType('series', 'series', $serieId);
    }

    /**
     * Get book files data by Author ID
     *
     * @param  int          $authorId
     *
     * @return array
     */
    public function loadByAuthorId($authorId)
    {
        return $this->getCollectionByType('authors', 'author', $authorId);
    }

    /**
     * Get book files data by Tag ID
     *
     * @param  int          $tagId
     *
     * @return array
     */
    public function loadByTagId($tagId)
    {
        return $this->getCollectionByType('tags', 'tag', $tagId);
    }

    /**
     * Get book files data by book ID
     *
     * @param  int          $tagId
     *
     * @return array
     */
    public function loadByBookId($bookId)
    {
        return $this->getBaseSelect()
            ->where('main.book = :book_id')
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
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
        if ($collection->count() === 0) {
            return $collection;
        }

        $bookIds = $collection->getAllIds();

        $stmt = $this->getBaseSelect()
            ->where('book_id IN (:book_id)')
            ->setParameter('book_id', $bookIds, Connection::PARAM_INT_ARRAY)
            ->execute();

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

            // Get the book that will receive the bookfiles
            $book = $collection->getById($row['book_id']);

            $bookFile = $this->setDataFromStatement($row);
            $bookFile->setDirectory($book->getPath());

            $book->getFiles()->add($bookFile);
        }
        return $collection;
    }

    /**
     * Retrieve a collection of book file (for books/series/tags ...)
     *
     * @param  string $tableName
     * @param  string $fieldName
     * @param  string $fieldValue
     *
     * @return array
     */
    private function getCollectionByType($tableName, $fieldName, $fieldValue)
    {
        return $this->getQueryBuilder()
            ->select(
                'data.id',
                'data.format',
                'data.uncompressed_size',
                'data.name',
                'books.path AS directory'
            )
            ->from(sprintf('books_%s_link', $tableName), 'main')
            ->innerJoin('main', 'data',  '', 'data.book = main.book')
            ->innerJoin('main', 'books', '', 'books.id = data.book')
            ->where(sprintf('main.%s = :value', $fieldName))
            ->setParameter('value', $fieldValue)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Base QB select getter
     *
     * @return Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getBaseSelect()
    {
        return $this->getQueryBuilder()
            ->select(
                'main.id',
                'main.book as book_id',
                'main.format',
                'main.uncompressed_size',
                'main.name'
            )
            ->from('data', 'main');
    }

    /**
     * Set data to entity from statement result
     *
     * @param array $result
     *
     * @return Common
     */
    public function setDataFromStatement(array $result)
    {
        $entity = $this->app['factory.bookfile']->getInstance($result['format']);
        return $entity->setData($result);
    }
}
