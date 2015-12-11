<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\BookFile;

use Cops\Core\AbstractRepository;
use Cops\Core\Entity\BookCollection;
use PDO;
use Doctrine\DBAL\Connection;

/**
 * BookFile repository
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookFileRepository extends AbstractRepository
{
    /**
     * Find by serie ID
     *
     * @param  int   $serieId
     *
     * @return array
     */
    public function findBySerieId($serieId)
    {
        return $this->findCollectionByType('series', 'series', $serieId);
    }

    /**
     * Find by author ID
     *
     * @param  int   $authorId
     *
     * @return array
     */
    public function findByAuthorId($authorId)
    {
        return $this->findCollectionByType('authors', 'author', $authorId);
    }

    /**
     * Find by tag ID
     *
     * @param  int   $tagId
     *
     * @return array
     */
    public function findByTagId($tagId)
    {
        return $this->findCollectionByType('tags', 'tag', $tagId);
    }

    /**
     * Find by book id
     *
     * @param  int   $bookId
     *
     * @return array
     */
    public function findByBookId($bookId)
    {
        return $this->getBaseSelect()
            ->addSelect('books.path as directory')
            ->where('main.book = :book_id')
            ->innerJoin('main', 'books', '', 'books.id = main.book')
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load bookfiles from a book collection
     *
     * @param  BookCollection $books
     *
     * @return array
     */
    public function findFromBooks(BookCollection $books)
    {
        return $this->getBaseSelect()
            ->addSelect('books.path as directory')
            ->where('book_id IN (:book_id)')
            ->innerJoin('main', 'books', '', 'books.id = main.book')
            ->setParameter('book_id', $books->getAllIds(), Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
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
    private function findCollectionByType($tableName, $fieldName, $fieldValue)
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
}
