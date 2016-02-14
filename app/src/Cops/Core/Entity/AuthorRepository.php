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
use Cops\Core\Entity\RepositoryInterface\AuthorRepositoryInterface;
use Cops\Core\StringUtils;
use Doctrine\DBAL\Connection;
use PDO;
use Cops\Core\Entity\Author;
use Cops\Core\Entity\BookCollection;

/**
 * Author repository
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AuthorRepository extends AbstractRepository implements AuthorRepositoryInterface
{
    /**
     * String utils instance
     * @var StringUtils
     */
    private $stringUtils;

    /**
     * Constructor
     *
     * @param StringUtils $utils
     */
    public function __construct(StringUtils $utils)
    {
        $this->stringUtils = $utils;
    }

    /**
     * Find by id
     *
     * @param  int    $authorId
     *
     * @return array
     */
    public function findById($authorId)
    {
        return $this->getQueryBuilder()
            ->select('*')
            ->from('authors', 'main')
            ->where('id = :author_id')
            ->setParameter('author_id', $authorId, PDO::PARAM_INT)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Save author
     *
     * @param Author $author
     *
     * @return int
     */
    public function save(Author $author)
    {
        if ($author->getId()) {
            $authorId = $author->getId();
            $this->update($author);
        } else {
            $authorId = $this->insert($author);
        }

        return $authorId;
    }

    /**
     * Insert author
     *
     * @param  Author $author
     *
     * @return int
     */
    protected function insert(Author $author)
    {
        $con = $this->getConnection();
        $con->insert('authors',
            array(
                'name' => $author->getName(),
                'sort' => $author->getSort(),
            ),
            array(
                PDO::PARAM_STR,
                PDO::PARAM_STR,
            )
        );

        return $con->lastInsertId();
    }

    /**
     * Update author
     *
     * @param  Author $author
     *
     * @return int    Number of updated lines
     */
    public function update(Author $author)
    {
        return $this->getQueryBuilder()
            ->update('authors')
            ->set('name', ':name')
            ->set('sort', ':sort')
            ->where('id = :id')
            ->setParameter('id',   $author->getId(),   PDO::PARAM_INT)
            ->setParameter('name', $author->getName(), PDO::PARAM_STR)
            ->setParameter('sort', $author->getSort(), PDO::PARAM_STR)
            ->execute();
    }

    /**
     * Delete author
     *
     * @param  Author $author
     *
     * @return bool
     */
    public function delete(Author $author)
    {
        return (bool) $this->getConnection()
            ->delete('authors',
            array(
                'id' => $author->getId(),
            ),
            array(
                PDO::PARAM_INT,
            )
        );
    }

    /**
     * Count authors by first letter
     *
     * @return array
     */
    public function countGroupedByFirstLetter()
    {
        return $this->getQueryBuilder()
            ->select(
                'DISTINCT UPPER(SUBSTR(sort, 1, 1)) AS first_letter',
                'COUNT(*) AS nb_author'
            )
            ->from('authors', 'main')
            ->innerJoin('main', 'books_authors_link', 'bal', 'main.id = bal.author')
            ->groupBy('first_letter')
            ->orderBy('first_letter')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all authors
     *
     * @return int
     */
    public function countAll()
    {
        return (int) $this->getQueryBuilder()
            ->select('COUNT(*)')
            ->from('authors', 'authors')
            ->execute()
            ->fetchColumn();
    }

    /**
     * Count book number written by author
     *
     * @param  int $authorId
     *
     * @return int
     */
    public function countBooks($authorId)
    {
        return (int) $this->getQueryBuilder()
            ->select('COUNT(*) AS nb_author')
            ->from('authors', 'main')
            ->innerJoin('main', 'books_authors_link', 'bal',   'bal.author = main.id')
            ->innerJoin('main', 'books',              'books', 'books.id = bal.book')
            ->where('main.id = :author_id')
            ->setParameter('author_id', $authorId, PDO::PARAM_INT)
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
            ->select('main.*', 'COUNT(bal.book) as book_count')
            ->from('authors', 'main')
            ->innerJoin('main', 'books_authors_link', 'bal', 'bal.author = main.id');

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
     * Load by bookId
     *
     * @param  int   $bookId
     *
     * @return array
     */
    public function loadByBookId($bookId)
    {
        return $this->getQueryBuilder()
            ->select('main.*')
            ->from('authors', 'main')
            ->innerJoin('main', 'books_authors_link', 'bal',   'bal.author = main.id')
            ->innerJoin('main', 'books',              'books', 'books.id = bal.book')
            ->where('books.id = :book_id')
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load author data from a book collection
     *
     * @param  BookCollection $books
     *
     * @return array
     */
    public function loadFromBooks(BookCollection $books)
    {
        return $this->getQueryBuilder()
            ->select(
                'main.*',
                'books.id AS bookId'
            )
            ->from('authors', 'main')
            ->innerJoin('main', 'books_authors_link', 'bal',   'bal.author = main.id')
            ->innerJoin('main', 'books',              'books', 'books.id = bal.book')
            ->where('books.id IN (:book_id)')
            ->setParameter('book_id', $books->getAllIds(), Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
