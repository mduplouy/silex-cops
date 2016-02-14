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
use Cops\Core\Entity\RepositoryInterface\TagRepositoryInterface;
use PDO;
use Doctrine\DBAL\Connection;
use Cops\Core\Entity\BookCollection;

/**
 * Tag repository
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class TagRepository extends AbstractRepository implements TagRepositoryInterface
{
    /**
     * Load a tag data
     *
     * @param  int   $tagId
     *
     * @return array
     */
    public function findById($tagId)
    {
        return $this->getQueryBuilder()
            ->select(
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
    }

    /**
     * Load tag by name
     *
     * @param  string $name
     *
     * @return array
     */
     public function findByName($name)
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
     * Load all tag names
     *
     * @return array
     */
    public function loadAllNames()
    {
        return $this->getQueryBuilder()
            ->select(
                'main.id',
                'main.name'
            )
            ->from('tags', 'main')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find by book ID
     *
     * @param  int   $bookId
     *
     * @return array
     */
    public function findByBookId($bookId)
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
     * @param  Book $book
     *
     * @return bool
     */
    public function deleteFromBook(Book $book)
    {
        $oldTags = $this->findByBookId($book->getId());

        // Delete book / tag relation
        $this->getQueryBuilder()
            ->delete('books_tags_link')
            ->where('book = :book_id')
            ->setParameter('book_id', $book->getId(), PDO::PARAM_INT)
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
     * @param  Tag $tag
     *
     * @return int Inserted ID
     */
    public function insert(Tag $tag)
    {
        $con = $this->getConnection();
        $con->insert('tags',
            array(
                'name' => $tag->getName(),
            ),
            array(
                PDO::PARAM_STR,
            )
        );
        return $con->lastInsertId();
    }

    /**
     * Associate tag to book
     *
     * @param  Tag  $tag
     * @param  Book $book
     *
     * @return int    Updated or inserted relation ID
     */
    public function associateToBook(Tag $tag, Book $book)
    {
        if (!$tag->getId()) {
            $tag->setId($this->insert($tag));
        }

        $con = $this->getConnection();
        $con->insert('books_tags_link',
            array(
                'book' => $book->getId(),
                'tag'  => $tag->getId()
            ),
            array(
                PDO::PARAM_INT,
                PDO::PARAM_STR,
            )
        );

        return $con->lastInsertId();
    }

    /**
     * Count all tags
     *
     * @return int
     */
    public function countAll()
    {
        return (int) $this->getQueryBuilder()
            ->select('count()')
            ->from('tags', 'main')
            ->execute()
            ->fetchColumn(0);
    }

    /**
     * Load tag from a book collection
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
            ->from('tags', 'main')
            ->innerJoin('main', 'books_tags_link', 'btl',   'main.id = btl.tag')
            ->innerJoin('main', 'books',           'books', 'books.id = btl.book')
            ->where('books.id IN (:book_id)')
            ->setParameter(':book_id', $books->getAllIds(), Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
