<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity;

use Cops\Core\AbstractRepository;
use Cops\Core\ApplicationAwareInterface;
use Cops\Core\Entity\RepositoryInterface\BookRepositoryInterface;
use Cops\Core\Application;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Doctrine\DBAL\Connection;

/**
 * Book repository
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookRepository extends AbstractRepository implements ApplicationAwareInterface, BookRepositoryInterface
{
    /**
     * Allow book exclusion
     * @var bool
     */
    private $hasExcludedBook = false;

    /**
     * Book id to be excluded from statement
     * @var int
     */
    private $excludeBookId;

    /**
     * Allow serie exclusion
     * @var bool
     */
    private $hasExcludedSerie = false;

    /**
     * Serie id to be excluded
     * @var int
     */
    private $excludeSerieId;

    /**
     * Application instance
     * @var Application
     */
    protected $app;

    /**
     * Set application
     *
     * @param  Application $app
     *
     * @return self
     */
    public function setApp(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Find by id
     *
     * @param  mixed            $bookId
     *
     * @return array
     */
    public function findById($bookId)
    {
        if (!is_array($bookId)) {
            $bookId = array($bookId);
        }

        $qb = $this->getBaseSelect()
            ->where('main.id IN (:book_id)')
            ->setParameter('book_id', $bookId, Connection::PARAM_INT_ARRAY);

        if (count($bookId) > 1) {
            $qb = $this->paginate($qb);
        } else {
            $this->totalRows = 1;
        }

        return $qb->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find books sorted by date
     *
     * @return array
     */
    public function findSortedByDate()
    {
        $qb = $this->getBaseSelect()
            ->addSelect('main.id')
            ->orderBy('main.timestamp', 'DESC');

        return $this->paginate($qb, array('select', 'join', 'groupBy', 'orderBy'))
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find all books
     *
     * @return array
     */
    public function findAll()
    {
        return $this->getQueryBuilder()
            ->select('main.*')
            ->from('books', 'main')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all books
     *
     * @return int
     */
    public function countAll()
    {
        return (int) $this->getQueryBuilder()
            ->select('count()')
            ->from('books', 'main')
            ->execute()
            ->fetchColumn(0);
    }

    /**
     * Find by serie id
     *
     * @param  int   $serieId
     *
     * @return array
     */
    public function findBySerieId($serieId)
    {
        $qb = $this->getBaseSelect()
            ->addSelect('main.id')
            ->andWhere('serie.id = :serie_id')
            ->orderBy('serie.name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->setParameter('serie_id', $serieId, PDO::PARAM_INT);

        $this->handleExcludedSerie($qb);
        $this->handleExcludedBook($qb);

        return $qb->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find by author id
     *
     * @param int    $authorId
     *
     * @return array
     */
    public function findByAuthorId($authorId)
    {
        $qb = $this->getBaseSelect()
            ->addSelect('main.id')
            ->leftJoin('main', 'books_authors_link', 'bal',    'bal.book = main.id')
            ->leftJoin('main', 'authors',            'author', 'author.id = bal.author')
            ->andWhere('author.id = :author_id')
            ->orderBy('serie.name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->setParameter('author_id', $authorId, PDO::PARAM_INT);

        $this->handleExcludedSerie($qb);
        $this->handleExcludedBook($qb);

        return $this->paginate($qb)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find by tag id
     *
     * @param int    $tagId
     *
     * @return array
     */
    public function findByTagId($tagId)
    {
        $qb = $this->getBaseSelect()
            ->addSelect('main.id')
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
     * Find by keyword
     *
     * @param  array  $keywords
     *
     * @return array
     */
    public function findByKeyword(array $keywords)
    {
        $qb = $this->getBaseSelect()
            ->addSelect('main.id')
            ->orderBy('serie_name')
            ->addOrderBy('series_index')
            ->addOrderBy('title')
            ->groupBy('main.id')
            ->resetQueryParts(array('where'));

        // Build the where clause - path includes author name so it's not added
        $andPath  = $qb->expr()->andX();
        $andSerie = $qb->expr()->andX();

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
     * Define excluded book id
     *
     * @param  int   $id
     *
     * @return $this
     */
    public function setExcludedBookId($id)
    {
        $this->hasExcludedBook = true;
        $this->excludeBookId = (int) $id;
        return $this;
    }

    /**
     * Define excluded serie id
     *
     * @param  int   $id
     *
     * @return $this
     */
    public function setExcludedSerieId($id)
    {
        $this->hasExcludedSerie = true;
        $this->excludeSerieId = (int) $id;
        return $this;
    }

    /**
     * Get the base select from QueryBuilder
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getBaseSelect()
    {
        $qb = $this->getQueryBuilder()
            ->select(
                'main.id',
                'main.title',
                'main.sort',
                'main.timestamp',
                'main.pubdate',
                'main.series_index',
                'main.isbn',
                'main.lccn',
                'main.path',
                'main.flags',
                'main.uuid',
                'main.has_cover AS cover_present',
                'main.last_modified',
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
            ->leftJoin('main', 'ratings',            'rating', 'brl.rating = rating.id');

        $this->handleExcludedBook($qb);
        $this->handleExcludedSerie($qb);

        return $qb;
    }

    /**
     * Handle excluded book
     *
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function handleExcludedBook(QueryBuilder $qb)
    {
        if ($this->hasExcludedBook) {
            $qb->andWhere('main.id != :exclude_book')
                ->setParameter('exclude_book', $this->excludeBookId, PDO::PARAM_INT);
            $this->hasExcludedBook = false;
            $this->excludeBookId = null;
        }

        return $qb;
    }

    /**
     * Handle excluded serie
     *
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function handleExcludedSerie(QueryBuilder $qb)
    {
        if ($this->hasExcludedSerie) {
            $qb->andWhere('serie.id IS NULL OR serie.id != :exclude_serie', PDO::PARAM_INT)
                ->setParameter('exclude_serie', $this->excludeSerieId);
            $this->hasExcludedSerie = false;
            $this->excludeSerieId = null;
        }

        return $qb;
    }
}

