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
        $result = $this->getQueryBuilder()
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
        return (int) $this->getQueryBuilder()
            ->select('COUNT(*)')
            ->from('tags', 'main')
            ->innerJoin('main', 'books_tags_link', 'btl', 'main.id = btl.tag')
            ->where('main.id = :tag_id')
            ->setParameter('tag_id', $tagId, PDO::PARAM_INT)
            ->execute()
            ->fetchColumn();
    }

    /**
     * Load all tags and count linked books
     *
     * @return array
     */
    public function loadAllWithBookCount()
    {
        $qb = $this->getQueryBuilder()
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
     * Load tag by name
     *
     * @param  string $name
     *
     * @return mixed
     */
     public function loadByName($name)
     {
         return $this->getQueryBuilder()
            ->select('main.*')
            ->from('tags', 'main')
            ->where('name = :name')
            ->setParameter('name', $name, PDO::PARAM_STR)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);
     }

    /**
     * Load all tag names
     *
     * @return array
     */
    public function loadAllNames()
    {
        return $this->getQueryBuilder()
            ->select('main.name')
            ->from('tags', 'main')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Load tags from book id
     *
     * @param  int $bookId
     *
     * @return array
     */
    public function loadByBookId($bookId)
    {
        return $this->getQueryBuilder()
            ->select(
                'tags.id',
                'tags.name',
                'COUNT(countTags.book) AS book_count'
            )
            ->from('books_tags_link', 'main')
            ->innerJoin('main', 'tags', 'tags', 'main.tag = tags.id')
            ->leftJoin('main', 'books_tags_link', 'countTags', 'tags.id = countTags.tag')
            ->where('main.book = :book_id')
            ->groupBy('main.tag')
            ->orderBy('tags.name')
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a tag
     *
     * @param  int $tagId
     *
     * @return int
     */
    public function delete($tagId)
    {
        return $this->getQueryBuilder()
            ->delete('tags')
            ->where('id = :tag_id')
            ->setParameter('tag_id', $tagId, PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Remove all tags associated to bookId
     *
     * @param  int $bookId
     *
     * @return bool
     */
    public function deleteByBookId($bookId)
    {
        $oldTags = $this->loadByBookId($bookId);

        // Delete book / tag relation
        $this->getQueryBuilder()
            ->delete('books_tags_link')
            ->where('book = :book_id')
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute();

        // Remove tag if there was only one book (specified one) associated
        foreach ($oldTags as $tag) {
            if ($tag['book_count'] == 1) {
                $this->delete($tag['id']);
            }
        }

        return true;
    }

    /**
     * Insert new name to database
     *
     * @param  string $name
     *
     * @return int    Inserted ID
     */
    public function insert($name)
    {
        $con = $this->getConnection();
        $con->insert('tags',
            array(
                'name' => $name,
            ),
            array(
                PDO::PARAM_STR,
            )
        );
        return $con->lastInsertId();
    }

    /**
     * Link a tag name to book ID
     *
     * @param  string $name
     * @param  int    $bookId
     *
     * @return int    Updated or inserted relation ID
     */
    public function linkToBookId($bookId)
    {
        $con = $this->getConnection();
        $con->insert('books_tags_link',
            array(
                'book' => $bookId,
                'tag'  => $this->getEntity()->getId()
            ),
            array(
                PDO::PARAM_INT,
                PDO::PARAM_STR,
            )
        );
        return $con->lastInsertId();
    }
}