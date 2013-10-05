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

use Cops\Model\Core;
use Cops\Exception\AuthorException;

/**
 * Author resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends \Cops\Model\Resource
{
    protected $_baseSelect = 'SELECT
        main.*
        FROM authors AS main';

    /**
     * Load an author data
     *
     * @param  int                $authorId
     * @param  \Cops\Model\Author $author
     *
     * @return \Cops\Model\Serie;
     */
    public function load($authorId, \Cops\Model\Author $author)
    {
        $result = $this->getConnection()
            ->fetchAssoc(
                $this->getBaseSelect(). ' WHERE id = ?',
                array(
                    (int) $authorId,
                )
            );

        if (empty($result)) {
            throw new AuthorException(sprintf(
                'Author width id %s not found',
                $authorId
            ));
        }

        return $author->setData($result);
    }

    /**
     * Load aggregated list of authors
     *
     * @return array();
     */
    public function getAggregatedList()
    {
        $db = $this->getConnection();

        $sql = 'SELECT
            DISTINCT UPPER(SUBSTR(sort, 1, 1)) AS first_letter,
            COUNT(*) AS nb_author
            FROM authors
            GROUP BY first_letter
            ORDER BY first_letter';

        return $db->fetchAll($sql);
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
        $sql = 'SELECT
            COUNT(*) FROM authors
            INNER JOIN books_authors_link ON authors.id = books_authors_link.author
            INNER JOIN books ON books_authors_link.book = books.id
            WHERE authors.id = ?';

        return (int) $this->getConnection()
            ->fetchColumn(
                $sql,
                array(
                    (int) $authorId,
                ),
                0
            );
    }

    /**
     * Retrieve collection based on first letter
     *
     * @param string|0           $letter
     * @param \Cops\Model\Author $author
     *
     * @return \Cops\Model\Author\Collection
     */
    public function getCollectionByFirstLetter($letter, $author)
    {
        $collection = $author->getCollection();

        $sql = $this->getBaseSelect();

        if ($letter != '#') {
            $sql .= ' WHERE UPPER(SUBSTR(sort, 1, 1)) = ?';
            $params = array($letter);
            $paramsType = array(\PDO::PARAM_STR);
        } else {
            $sql .= ' WHERE UPPER(SUBSTR(sort, 1, 1)) NOT IN (?)';
            $params = array(Core::getLetters());
            $paramsType = array(\Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
        }
        $sql .= ' ORDER BY sort';

         $stmt = $this->getConnection()
            ->executeQuery(
                $sql,
                $params,
                $paramsType
            )
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach($stmt as $result) {
            $author = clone($author);
            $author->setData($result);
            $collection->add($author);
        }
        return $collection;
    }
}
