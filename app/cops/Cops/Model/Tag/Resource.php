<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Tag;

use Cops\Model\ResourceAbstract;
use Cops\Exception\TagException;
use PDO;
use Doctrine\DBAL\Connection;

/**
 * Tag resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends ResourceAbstract
{
    /**
     * Load a tag data
     *
     * @param  int          $tagId
     *
     * @throws TagException
     *
     * @return array
     */
    public function load($tagId)
    {
        $result = $this->getBaseSelect()
            ->select(
                'main.id',
                'main.name',
                'COUNT(btl.id) AS book_count'
            )
            ->from('tags', 'main')
            ->leftJoin('main', 'books_tags_link', 'btl', 'main.id = btl.tag')
            ->where('main.id = :tag_id')
            ->groupBy('main.id')
            ->setParameter('tag_id', $tagId, PDO::PARAM_INT)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            throw new TagException(sprintf(
                'Tag width id %s not found',
                $tagId
            ));
        }

        return $result;
    }

    /**
     * Count book number linked to tag
     *
     * @param  int $tagId
     *
     * @return int
     */
    public function countBooks($tagId)
    {
        $sql = 'SELECT
            COUNT(*) FROM tags
            INNER JOIN books_tags_link ON tags.id = books_tags_link.tag
            WHERE tags.id = ?';

        return (int) $this->getConnection()
            ->fetchColumn(
                $sql,
                array(
                    (int) $tagId,
                ),
                0
            );
    }

    /**
     * Load all tags and count linked books
     *
     * @return array
     */
    public function loadAll()
    {
        $qb = $this->getBaseSelect()
            ->select(
                'main.id',
                'main.name',
                'COUNT(btl.book) AS book_count'
            )
            ->from('tags', 'main')
            ->innerJoin('main', 'books_tags_link', 'btl', 'main.id = btl.tag')
            ->groupBy('main.id');

        return $this->paginate($qb, array('select', 'join', 'groupBy', 'orderBy'))
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load tags from book id
     */
    public function loadByBookId($bookId)
    {
        return $this->getBaseSelect()
            ->select(
                'main.id',
                'main.name',
                'COUNT(btl.book) AS book_count'
            )
            ->from('tags', 'main')
            ->innerJoin('main', 'books_tags_link', 'btl', 'main.id = btl.tag')
            ->where('btl.book = :book_id')
            ->groupBy('main.id')
            ->orderBy('main.name')
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

}