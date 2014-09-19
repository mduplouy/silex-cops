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

use Cops\Model\ResourceAbstract;
use Cops\Exception\BookException;
use Cops\Model\Core;
use Cops\Model\Book\Collection;
use PDO;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;

/**
 * Book resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends ResourceAbstract
{
    /**
     * Allow book exclusion
     * @var bool
     */
    private $_hasExcludedBook = false;

    /**
     * Book id to be excluded from statement
     * @var int
     */
    private $_excludeBookId;

    /**
     * Allow serie exclusion
     * @var bool
     */
    private $_hasExcludedSerie = false;

    /**
     * Serie id to be excluded
     * @var int
     */
    private $_excludeSerieId;

    /**
     * Load a book data
     *
     * @param  int              $bookId
     * @param  \Cops\Model\Book $book
     *
     * @return array
     */
    public function load($bookId)
    {
        $result = $this->getBaseSelect()
            ->where('main.id = :book_id')
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            throw new BookException(sprintf('Book width id %s not found', $bookId));
        }

        return $result;
    }

    /**
     * Load latest added books from database
     *
     * @param  int   $nb  Number of items to load
     *
     * @return array
     */
    public function loadLatest($nb)
    {
        return $this->getBaseSelect()
            ->orderBy('main.timestamp', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($nb)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load all books
     *
     * @return array
     */
    public function loadAll()
    {
        return $this->getQueryBuilder()
            ->select('main.*')
            ->from('books', 'main')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load books by serie ID
     *
     * @param  int          $serieId
     *
     * @return array
     */
    public function loadBySerieId($serieId)
    {
        return $this->getBaseSelect()
            ->andWhere('serie.id = :serie_id')
            ->orderBy('serie.name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->setParameter('serie_id', $serieId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load books by author ID
     *
     * @param int              $authorId
     * @param bool             $addFiles
     *
     * @return array
     */
    public function loadByAuthorId($authorId)
    {
        return $this->getBaseSelect()
            ->leftJoin('main', 'books_authors_link', 'bal',    'bal.book = main.id')
            ->leftJoin('main', 'authors',            'author', 'author.id = bal.author')
            ->andWhere('author.id = :author_id')
            ->orderBy('serie.name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->setParameter('author_id', $authorId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load books collection by tag ID
     *
     * @param int              $tagId
     *
     * @return array
     */
    public function loadByTagId($tagId)
    {
        $qb = $this->getBaseSelect()
            ->innerJoin('main', 'books_tags_link', 'btl', 'main.id = btl.book')
            ->innerJoin('main', 'tags'           , 'tag', 'tag.id  = btl.tag')
            ->andWhere('tag.id = :tagid')
            ->orderBy('serie.name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->setParameter('tagid', $tagId, PDO::PARAM_INT);

        return $this->paginate($qb)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load collection based on keyword
     *
     * @param  array        $keywords
     *
     * @return array
     */
    public function loadByKeyword($keywords) {

        $qb = $this->getBaseSelect()
            ->leftJoin('main', 'books_tags_link', 'btl', 'btl.book = main.id')
            ->leftJoin('main',  'tags',           'tag', 'tag.id = btl.tag')
            ->orderBy('serie_name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->groupBy('main.id')
            ->resetQueryParts(array('where'));

        // Build the where clause
        $andPath  = $qb->expr()->andX();
        $andSerie = $qb->expr()->andX();
        //$andSerie->add($qb->expr()->isNotNull('serie_name'));
        foreach ($keywords as $keyword) {
            $andPath->add(
                $qb->expr()->like('main.path', $this->getConnection()->quote('%'.$keyword.'%'))
            );
            $andSerie->add(
                $qb->expr()->like('serie.sort', $this->getConnection()->quote('%'.$keyword.'%'))
            );
        }

        $qb->orWhere($andPath, $andSerie);

        return $this->paginate($qb)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Set data from statement
     *
     * @param array            $result The result array from select stmt
     *
     * @return array
     */
    public function setDataFromStatement(array $result)
    {
        $myBook = parent::setDataFromStatement($result);

        if (!empty($result['serie_id'])) {
            $myBook->getSerie()->setData(array(
                'id'   => $result['serie_id'],
                'name' => $result['serie_name'],
                'sort' => $result['serie_sort'],
            ));
        }

        return $myBook;
    }

    /**
     * Define excluded book id
     *
     * @param int $id
     *
     * @return Resource
     */
    public function setExcludedBookId($id) {
        $this->_hasExcludedBook = true;
        $this->_excludeBookId = (int) $id;
        return $this;
    }

    /**
     * Define excluded serie id
     *
     * @param int $id
     *
     * @return Resource
     */
    public function setExcludedSerieId($id) {
        $this->_hasExcludedSerie = true;
        $this->_excludeSerieId = (int) $id;
        return $this;
    }

    /**
     * Update author & author_sort
     *
     * @param int   $id
     * @param array $authors
     *
     * @return bool
     */
    public function updateAuthor($id, $authors)
    {
        $con = $this->getConnection();
        $con->beginTransaction();

        try {
            // Delete book <=> author link
            $con->createQueryBuilder()
                ->delete('books_authors_link')
                ->where('book = :book_id')
                ->setParameter('book_id', $id, PDO::PARAM_INT)
                ->execute();

            $allAuthorsSort = array();
            foreach ($authors as $authorName) {
                $allAuthorsSort[] = $this->updateAuthorSortNameAndLink($id, $authorName);
            }

            // Update author_sort in book table (no relation)
            $con->update(
                'books',
                array('author_sort' => implode(' & ', $allAuthorsSort)),
                array('id'          => $id),
                array(
                    PDO::PARAM_STR,
                    PDO::PARAM_INT,
                )
            );
            $con->commit();
            return true;
        } catch (\Exception $e) {         // @fixme pop exception message to the user
            $con->rollback();
            return false;
        }
    }

    /**
     * Update author name and author <=> book link
     *
     * @param int    $id         Book ID
     * @param string $authorName Author name
     *
     * @return string $sortName
     *
     */
    private function updateAuthorSortNameAndLink($bookId, $authorName)
    {
        $sortName = $this->app['model.calibre']->getAuthorSortName($authorName);

        // Get author id if author name already exists
        $authorId = $this->getQueryBuilder()
            ->select('id')
            ->from('authors', 'main')
            ->where('main.name = :author_name')
            ->setParameter('author_name', $authorName, PDO::PARAM_STR)
            ->execute()
            ->fetchColumn();

        // Save author data (update existing or insert new one)
        $author = $this->app['model.author'];
        $author
            ->setName($authorName)
            ->setSort($sortName);
        if ($authorId) {
            $author->setId($authorId);
        }
        $authorId = $author->save();

        // Create new book <=> author link
        $this->getConnection()->insert(
            'books_authors_link',
            array(
                'book'   => $bookId,
                'author' => $authorId
            ),
            array(
                PDO::PARAM_INT,
                PDO::PARAM_INT
            )
        );
        return $sortName;
    }

    /**
     * Update title & title sort
     *
     * @param int   $id
     * @param array $title
     *
     * @return bool
     */
    public function updateTitle($id, $title)
    {
        $con = $this->getConnection();

        $con->beginTransaction();

        try {
            $bookLang = $this->getBookLanguageCode($id);
            $titleSort = $this->app['model.calibre']->getTitleSort($title, $bookLang);

            $con->update(
                'books',
                array(
                    'title' => $title,
                    'sort'  => $titleSort,
                ),
                array('id'  => $id),
                array(
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    PDO::PARAM_INT,
                )
            );
            $con->commit();

            $this->getEntity()->setTitle($title);

        } catch (\Exception $e) {
            $con->rollback();
        }
        return true;
    }

    /**
     * Update publication date
     *
     * @param int       $id
     * @param \DateTime $pubDate
     *
     * @return bool
     */
    public function updatePublicationDate($id, \DateTime $pubDate)
    {
        return (bool) $this->getConnection()
            ->update(
                'books',
                array('pubdate' => $pubDate),
                array('id' => $id),
                array(
                    'datetime',
                    PDO::PARAM_INT
                )
            );
    }

    /**
     * Update comment
     *
     * @param int       $id
     * @param string    $comment
     *
     * @return bool
     */
    public function updateComment($id, $comment)
    {
        return (bool) $this->getConnection()
            ->update(
                'comments',
                array('text' => $comment),
                array('book' => $id),
                array(
                    PDO::PARAM_STR,
                    PDO::PARAM_INT
                )
            );
    }

    /**
     * Get book language code from DB
     *
     * @param  int    $bookId
     *
     * @return string
     */
    public function getBookLanguageCode($bookId)
    {
        $lang = $this->getQueryBuilder()
            ->select('lang.lang_code')
            ->from('books_languages_link', 'main')
            ->innerJoin('main', 'books',     'books', 'books.id = main.book')
            ->innerJoin('main', 'languages', 'lang',  'main.lang_code = lang.id')
            ->where('main.book = :id')
            ->setParameter('id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetchColumn();

        if ($lang) {
            $lang = substr($lang, 0, 2);
        }
        return $lang;
    }

    /**
     * Get the base select from QueryBuilder
     *
     * @return Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getBaseSelect()
    {
        $qb = $this->getQueryBuilder()
            ->select(
                'main.*',
                'com.text AS comment',
                'rating.rating AS rating',
                'serie.id AS serie_id',
                'serie.name AS serie_name',
                'serie.sort AS serie_sort'
            )
            ->from('books', 'main')
            ->leftJoin('main', 'comments',           'com',    'com.book = main.id')
            ->leftJoin('main', 'books_series_link',  'bsl',    'bsl.book = main.id')
            ->leftJoin('main', 'series',             'serie',  'serie.id = bsl.series')
            ->leftJoin('main', 'books_ratings_link', 'brl',    'brl.book = main.id')
            ->leftJoin('main', 'ratings',            'rating', 'brl.rating = rating.id')
            ->where('1');

        if ($this->_hasExcludedBook) {
            $qb->andWhere('main.id != :exclude_book')
                ->setParameter('exclude_book', $this->_excludeBookId, PDO::PARAM_INT);
            $this->_hasExcludedBook = false;
            $this->_excludeBookId = null;
        }
        if ($this->_hasExcludedSerie) {
            $qb->andWhere('serie.id IS NULL OR serie.id != :exclude_serie', PDO::PARAM_INT)
                ->setParameter('exclude_serie', $this->_excludeSerieId);
            $this->_hasExcludedSerie = false;
            $this->_excludeSerieId = null;
        }
        return $qb;
    }
}

