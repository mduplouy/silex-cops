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

use Cops\Core\AbstractCollection;
use Cops\Core\Entity\BookFile\BookFileCollection;
use Cops\Core\Entity\AuthorCollection;
use Cops\Core\Entity\TagCollection;
use Cops\Core\Entity\Exception\BookNotFoundException;

/**
 * Book collection
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookCollection extends AbstractCollection
{
    /**
     * Find lastest added books
     *
     * @param int  $nb  Number of items to load
     *
     * @return $this
     */
    public function getLatest($nb)
    {
        return $this->setDataFromArray(
            $this->getRepository()->setMaxResults($nb)->findSortedByDate()
        );
    }

    /**
     * Find sorted by date
     *
     * @return $this
     */
    public function findSortedByDate()
    {
        return $this->setDataFromArray(
            $this->getRepository()->findSortedByDate()
        );
    }

    /**
     * Count all books
     *
     * @return int
     */
    public function countAll()
    {
        return (int) $this->getRepository()->countAll();
    }

    /**
     * Find by id
     *
     * @param array $ids
     *
     * @return $this
     */
    public function findById(array $ids)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findById($ids)
        );
    }

    /**
     * Find all books
     *
     * @return $this
     */
    public function findAll()
    {
        return $this->setDataFromArray(
            $this->getRepository()->findAll()
        );
    }

    /**
     * Find by author id
     *
     * @param  int $authorId
     *
     * @return $this
     */
    public function findByAuthorId($authorId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByAuthorId($authorId)
        );
    }

    /**
     * Find by serie id
     *
     * @param  int $serieId
     *
     * @return $this
     */
    public function findBySerieId($serieId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findBySerieId($serieId)
        );
    }

    /**
     * Find by tag id
     *
     * @param  int $tagId
     *
     * @return $this
     */
    public function findByTagId($tagId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByTagId($tagId)
        );
    }

    /**
     * Find by keyword
     *
     * @param  array $keyword
     *
     * @return $this
     */
    public function findByKeyword(array $keyword)
    {
        $result = $this->getRepository()->findByKeyword($keyword);

        if (empty($result)) {
            throw new BookNotFoundException(sprintf('No book found matching "%s"', implode(', ', $keyword)));
        }

        return $this->setDataFromArray($result);
    }

    /**
     * Find other books from same author
     *
     * @param  Book $book
     *
     * @return $this
     */
    public function findOthersFromSameAuthor(Book $book)
    {
        $authors = $book->getAuthors();

        if ($authors->count() == 1) {

            // Force int cast
            $authorId = $authors->getAllIds();
            $authorId = current($authorId);

            $this->getRepository()
                ->setExcludedBookId($book->getId())
                ->setExcludedSerieId($book->getSerie()->getId());
            // Only one id in collection
            $this->findByAuthorId($authorId);
        }

        return $this;
    }

    /**
     * Find others books from same serie
     *
     * @param  Book $book
     *
     * @return $this
     */
    public function findOthersFromSameSerie(Book $book)
    {
        $this->getRepository()
            ->setExcludedBookId($book->getId());

        return $this->findBySerieId($book->getSerie()->getId());
    }

    /**
     * Add bookfiles to current book collection
     *
     * @param BookFileCollection $bookFiles
     *
     * @return $this
     */
    public function addBookFiles(BookFileCollection $bookFiles)
    {
        foreach ($bookFiles->findFromBooks($this) as $bookFile) {
            $this->getById($bookFile->getBookId())
                ->getFiles()
                ->add($bookFile);
        }

        return $this;
    }

    /**
     * Load and add authors to current book collection
     *
     * @param AuthorCollection authors
     *
     * @return $this
     */
    public function addAuthors(AuthorCollection $authors)
    {
        foreach ($authors->findFromBooks($this) as $author) {
            $this->getById($author->getBookId())
                ->getAuthors()
                ->add($author);
        }

        return $this;
    }

    /**
     * Load and add tags to current book collection
     *
     * @param  TagCollection $tags
     *
     * @return $this
     */
    public function addTags(TagCollection $tags)
    {
        foreach ($tags->findFromBooks($this) as $tag) {
            $this->getById($tag->getBookId())
                ->getTags()
                ->add($tag);
        }

        return $this;
    }
}
