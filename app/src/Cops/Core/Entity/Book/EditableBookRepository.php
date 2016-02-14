<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\Book;

use Cops\Core\Entity\BookRepository;
use Cops\Core\Entity\RepositoryInterface\Book\EditableBookRepositoryInterface;
use Cops\Core\Entity\Book as BookEntity;
use PDO;

/**
 * Editable Book repository
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class EditableBookRepository extends BookRepository implements EditableBookRepositoryInterface
{
    /**
     * Update author & author_sort
     *
     * @param int   $bookId
     * @param array $authors
     *
     * @return bool
     */
    public function updateAuthor($bookId, $authors)
    {
        $con = $this->getConnection();
        $con->beginTransaction();

        try {
            // Delete book <=> author link
            $con->createQueryBuilder()
                ->delete('books_authors_link')
                ->where('book = :book_id')
                ->setParameter('book_id', $bookId, PDO::PARAM_INT)
                ->execute();

            $allAuthorsSort = array();
            foreach ($authors as $authorName) {
                $allAuthorsSort[] = $this->updateAuthorSortNameAndLink($bookId, $authorName);
            }

            // Update author_sort in book table (no relation)
            $con->update(
                'books',
                array('author_sort' => implode(' & ', $allAuthorsSort)),
                array('id'          => $bookId),
                array(
                    PDO::PARAM_STR,
                    PDO::PARAM_INT,
                )
            );
            $con->commit();
            return true;
        } catch (\Exception $e) {
            // @todo pop exception message to the user
            $con->rollback();
            return false;
        }
    }

    /**
     * Update author name and author <=> book link
     *
     * @param int    $bookId     Book ID
     * @param string $authorName Author name
     *
     * @return string $sortName
     *
     */
    private function updateAuthorSortNameAndLink($bookId, $authorName)
    {
        $sortName = $this->app['calibre-util']->getAuthorSortName($authorName);

        // Get author id if author name already exists
        $authorId = $this->getQueryBuilder()
            ->select('id')
            ->from('authors', 'main')
            ->where('main.name = :author_name')
            ->setParameter('author_name', $authorName, PDO::PARAM_STR)
            ->execute()
            ->fetchColumn();

        // Save author data (update existing or insert new one)
        $author = $this->app['entity.author'];
        $author->setName($authorName)
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
     * @param int    $bookId
     * @param string $title
     *
     * @return bool
     */
    public function updateTitle($bookId, $title)
    {
        $con = $this->getConnection();

        try {
            $con->beginTransaction();

            $bookLang = $this->getBookLanguageCode($bookId);
            $titleSort = $this->app['calibre-util']->getTitleSort($title, $bookLang);

            $con->update(
                'books',
                array(
                    'title' => $title,
                    'sort'  => $titleSort,
                ),
                array('id'  => $bookId),
                array(
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    PDO::PARAM_INT,
                )
            );

            $con->commit();
            $return = true;

        } catch (\Exception $e) {
            $con->rollback();
            $return = false;
        }

        return $return;

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
     * Update publication date
     *
     * @param int       $bookId
     * @param \DateTime $pubDate
     *
     * @return bool
     */
    public function updatePubDate($bookId, \DateTime $pubDate)
    {
        return (bool) $this->getConnection()
            ->update(
                'books',
                array('pubdate' => $pubDate),
                array('id' => $bookId),
                array(
                    'datetime',
                    PDO::PARAM_INT
                )
            );
    }

    /**
     * Update comment
     *
     * @param int       $bookId
     * @param string    $comment
     *
     * @return bool
     */
    public function updateComment($bookId, $comment)
    {
        return (bool) $this->getConnection()
            ->update(
                'comments',
                array('text' => $comment),
                array('book' => $bookId),
                array(
                    PDO::PARAM_STR,
                    PDO::PARAM_INT
                )
            );
    }
}

