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
use Cops\Core\Entity\RepositoryInterface\SerieRepositoryInterface;
use Cops\Core\StringUtils;
use PDO;
use Doctrine\DBAL\Connection;

/**
 * Serie repository
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SerieRepository extends AbstractRepository implements SerieRepositoryInterface
{
    /**
     * String utils instance
     * @var StringUtils
     */
    protected $stringUtils;

    /**
     * Constructor
     *
     * @param StringUtils $stringUtils
     */
    public function __construct(StringUtils $stringUtils)
    {
        $this->stringUtils = $stringUtils;
    }

    /**
     * Find by id
     *
     * @param  int   $serieId
     *
     * @return array|false
     */
    public function findById($serieId)
    {
        return $this->getQueryBuilder()
            ->select('main.*')
            ->from('series', 'main')
            ->where('id = :serie_id')
            ->setParameter('serie_id', $serieId, PDO::PARAM_INT)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find by name
     *
     * @param  string $name
     *
     * @return array
     */
    public function findByName($name)
    {
        return (array) $this->getQueryBuilder()
            ->select('main.*')
            ->from('series', 'main')
            ->where('name = :name')
            ->setParameter('name', $name, PDO::PARAM_STR)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Count book number in serie
     *
     * @param  int $serieId
     *
     * @return int
     */
    public function countBooks($serieId)
    {
        return (int) $this->getQueryBuilder()
            ->select('COUNT(*)')
            ->from('series', 'main')
            ->innerJoin('main', 'books_series_link', 'bsl',   'bsl.series = main.id')
            ->innerJoin('main', 'books',             'books', 'bsl.book = books.id')
            ->where('main.id = :serie_id')
            ->setParameter('serie_id', $serieId, PDO::PARAM_INT)
            ->execute()
            ->fetchColumn();
    }

    /**
     * Count series by first letter
     *
     * @return array
     */
    public function countGroupedByFirstLetter()
    {
        return $this->getQueryBuilder()
            ->select(
                'DISTINCT UPPER(SUBSTR(sort, 1, 1)) AS first_letter',
                'COUNT(*) AS nb_serie'
            )
            ->from('series', 'series')
            ->groupBy('first_letter')
            ->orderBy('first_letter')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count series
     *
     * @return int
     */
    public function countAll()
    {
        return (int) $this->getQueryBuilder()
            ->select('COUNT(*)')
            ->from('series', 'main')
            ->execute()
            ->fetchColumn();
    }

    /**
     * Find by first letter
     *
     * @param string  $letter
     *
     * @return array
     */
    public function findByFirstLetter($letter)
    {
        $qb = $this->getQueryBuilder()
            ->select('main.*', 'COUNT(bsl.series) AS book_count')
            ->from('series', 'main')
            ->innerJoin('main', 'books_series_link', 'bsl', 'bsl.series = main.id')
            ->where('main.id = :serie_id');

        if ($letter !== '#') {
            $qb->where('UPPER(SUBSTR(sort, 1, 1)) = ?')
                ->setParameter(1, $letter, PDO::PARAM_STR);
        } else {
            $qb->where('UPPER(SUBSTR(sort, 1, 1)) NOT IN (:letters)')
                ->setParameter('letters', $this->stringUtils->getLetters(), Connection::PARAM_STR_ARRAY);
        }

        return $qb->groupBy('main.id')
            ->orderBy('sort')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert new serie into database
     *
     * @param  Serie $serie
     *
     * @return int Inserted ID
     */
    public function insert(Serie $serie)
    {
        $con = $this->getConnection();
        $con->insert('series',
            array(
                'name' => $serie->getName(),
                'sort' => $serie->getSort(),
            ),
            array(
                PDO::PARAM_STR,
                PDO::PARAM_STR,
            )
        );
        return $con->lastInsertId();
    }

    /**
     * Associate serie to book
     *
     * @param  Serie $serie
     * @param  Book  $book
     *
     * @return int    Updated or inserted relation ID
     */
    public function associateToBook(Serie $serie, Book $book)
    {
        if (!$serie->getId()) {
            $serie->setId($this->insert($serie));
        }

        $this->getQueryBuilder()
            ->delete('books_series_link')
            ->where('book = :book_id')
            ->setParameter('book_id', $book->getId(), PDO::PARAM_INT)
            ->execute();

        $con = $this->getConnection();
        $con->insert('books_series_link',
            array(
                'book'   => $book->getId(),
                'series' => $serie->getId()
            ),
            array(
                PDO::PARAM_INT,
                PDO::PARAM_INT,
            )
        );

        return $con->lastInsertId();
    }
}
