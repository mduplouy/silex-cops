<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\BookFile;

use Cops\Core\AbstractCollection;
use Cops\Core\CollectionableInterface;
use Cops\Core\Entity\Book;
use Cops\Core\Entity\BookCollection;
use Cops\Core\Entity\BookFile\FormatUnavailableException;

/**
 * BookFile collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookFileCollection extends AbstractCollection
{
    /**
     * Default file format
     */
    const DEFAULT_FILE_FORMAT = 'epub';

    /**
     * Format mapping
     * @var array
     */
    private $formatMapping = array();

    /**
     * @inheritDoc
     *
     * Overloaded to add format mapping
     *
     * @throws \InvalidArgumentException
     */
    public function add(CollectionableInterface $element)
    {
        if (!$element instanceof AdapterInterface) {
            throw new \InvalidArgumentException('Element must implement AdapterInterface');
        }

        parent::add($element);
        $this->formatMapping[$element->getBookId()][$element->getFormat()] = count($this->elements) - 1;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * Overrided to build entity based on format
     */
    public function setDataFromArray(array $elementsData)
    {
        $entityKey = str_replace('collection', 'factory', $this->getServiceKey());
        $factory = $this->app[$entityKey];

        foreach ($elementsData as $elementData) {
            $this->add(
                $factory->getInstance($elementData['format'])->setDataFromArray($elementData)
            );
        }

        return $this;
    }

    /**
     * Find by format and book id
     *
     * @param  string $format
     * @param  int    $bookId
     *
     * @return \Cops\Core\BookFile\AdapterInterface
     *
     * @throws FormatUnavailableException
     */
    public function findByFormatAndBookId($format, $bookId)
    {
        if ($format === null) {
            $format = self::DEFAULT_FILE_FORMAT;
        }

        if (isset($this->formatMapping[$bookId][$format])) {
            $collectionKey = $this->formatMapping[$bookId][$format];
            return $this->elements[$collectionKey];
        }

        throw new FormatUnavailableException(
            sprintf('Could not get %s format for book id %s', $format, $bookId)
        );
    }

    /**
     * Find by serie ID
     *
     * @param  int   $serieId
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
     * Find by author ID
     *
     * @param  int   $authorId
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
     * Find from book collection
     *
     * @param BookCollection $books
     *
     * @return AuthorCollection
     */
    public function findFromBooks(BookCollection $books)
    {
        if ($books->count()) {
            $this->setDataFromArray(
                $this->getRepository()->findFromBooks($books)
            );
        }

        return $this;
    }

    /**
     * Find from book
     *
     * @param \Cops\Model\Book
     *
     * @return Collection
     */
    public function findFromBook(Book $book)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByBookId($book->getId())
        );
    }

    /**
     * Find by tag ID
     *
     * @param int $tagId
     *
     * @return Collection
     */
    public function findByTagId($tagId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByTagId($tagId)
        );
    }
}
