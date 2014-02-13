<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Author;

use Cops\Model\ResourceAbstract;
use Cops\Model\Core;
use Cops\Exception\AuthorException;
use Doctrine\DBAL\Connection;
use PDO;

/**
 * Author resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends ResourceAbstract
{
    /**
     * Load an author data
     *
     * @param  int    $authorId
     *
     * @return array
     */
    public function load($authorId)
    {
        $result = $this->getQueryBuilder()
            ->select('*')
            ->from('authors', 'main')
            ->where('id = :author_id')
            ->setParameter('author_id', $authorId, PDO::PARAM_INT)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            throw new AuthorException(sprintf(
                'Author width id %s not found',
                $authorId
            ));
        }
        return $result;
    }

    /**
     * Insert author
     *
     * @return int
     */
    public function insert()
    {
        $con = $this->getConnection();
        $con->insert('authors',
            array(
                'name' => $this->getEntity()->getName(),
                'sort' => $this->getEntity()->getSort(),
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
     * @return int
     */
    public function update()
    {
        return $this->getQueryBuilder()
            ->update('authors')
            ->set('name', ':author_name')
            ->where('id = :author_id')
            ->setParameter('author_id',   $authorId,   PDO::PARAM_INT)
            ->setParameter('author_name', $authorName, PDO::PARAM_STR)
            ->execute();
    }

    /**
     * Load aggregated list of authors
     *
     * @return array();
     */
    public function getAggregatedList()
    {
        return $this->getQueryBuilder()
            ->select(
                'DISTINCT UPPER(SUBSTR(sort, 1, 1)) AS first_letter',
                'COUNT(*) AS nb_author'
            )
            ->from('authors', 'main')
            ->groupBy('first_letter')
            ->orderBy('first_letter')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
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
     * Load based on first letter
     *
     * @param string        $letter
     *
     * @return array
     */
    public function loadByFirstLetter($letter)
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
                ->setParameter('letters', Core::getLetters(), Connection::PARAM_STR_ARRAY);
        }

        return $qb->groupBy('main.id')
            ->orderBy('sort')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);

    }
}
