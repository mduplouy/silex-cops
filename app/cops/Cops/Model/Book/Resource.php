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
        $result = $this->getBaseSelect()
            ->where('main.id = :book_id')
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);

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
        return $this->getBaseSelect()
            ->orderBy('main.timestamp', 'DESC')
            ->setMaxResults($nb)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
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
        return $this->getBaseSelect()
            ->where('serie.id = :serie_id')
            ->orderBy('serie.name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->setParameter('serie_id', $serieId)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
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
        return $this->getBaseSelect()
            ->where('author.id = :author_id')
            ->orderBy('serie.name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->setParameter('author_id', $authorId)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
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
        return $this->getBaseSelect()
            ->innerJoin('main', 'books_tags_link', 'btl', 'main.id = btl.book')
            ->innerJoin('main', 'tags'           , 'tag', 'tag.id  = btl.tag')
            ->where('tag.id = :tagid')
            ->orderBy('serie.name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->setParameter('tagid', $tagId)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load collection based on keyword
     *
     * @param  array        $keywords
     *
     * @return PDOStatement
     */
    public function loadByKeyword($keywords) {

        $qb = $this->getBaseSelect()
            ->leftJoin('main', 'books_tags_link', 'btl', 'btl.book = main.id')
            ->leftJoin('main',  'tags',           'tag', 'tag.id = btl.tag')
            ->orderBy('serie_name')
            ->addOrderBy('series_index')
            ->addOrderBy('title');

        // Build the where clause
        $or = $qb->expr()->orX();
        foreach($keywords as $keyword) {
            $or->add(
                $qb->expr()->Like('main.path', $this->getConnection()->quote('%'.$keyword.'%'))
            );
        }

        $stmt = $qb->where($or)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);

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


    public function getBaseSelect()
    {
        $qb = $this->getConnection()->createQueryBuilder();

        return $qb->select(
                'main.*',
                'com.text AS comment',
                'rating.rating AS rating',
                'author.id AS author_id',
                'author.name AS author_name',
                'author.sort AS author_sort',
                'serie.id AS serie_id',
                'serie.name AS serie_name',
                'serie.sort AS serie_sort'
            )
            ->from('books', 'main')
            ->leftJoin('main', 'comments',           'com',    'com.book = main.id')
            ->leftJoin('main', 'books_authors_link', 'bal',    'bal.book = main.id')
            ->leftJoin('main', 'authors',            'author', 'author.id = bal.author')
            ->leftJoin('main', 'books_series_link',  'bsl',    'bsl.book = main.id')
            ->leftJoin('main', 'series',             'serie',  'serie.id = bsl.series')
            ->leftJoin('main', 'books_ratings_link', 'brl',    'brl.book = main.id')
            ->leftJoin('main', 'ratings'           , 'rating', 'brl.rating = rating.id');
    }
}

