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

use Cops\Core\AbstractEntity;
use Cops\Core\CollectionableInterface;
use Cops\Core\Entity\AuthorCollection;
use Cops\Core\Cover;
use Cops\Core\Entity\Serie;
use Cops\Core\Entity\TagCollection;
use Cops\Core\Entity\BookFile\BookFileCollection;
use Cops\Core\Entity\Exception\BookNotFoundException;

/**
 * Book entity
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Book extends AbstractEntity implements BookInterface, CollectionableInterface
{
    /**
     * Repository interface to be checked
     */
    const REPOSITORY_INTERFACE = 'Cops\Core\Entity\RepositoryInterface\BookRepositoryInterface';

    /**
     * Object ID
     * @var int
     */
    private $id;

    /**
     * Publication date
     * @var \DateTime|null
     */
    private $pubdate;

    /**
     * Title
     * @var string
     */
    private $title;

    /**
     * Sort
     * @var string
     */
    private $sort;

    /**
     * Has cover
     * @var bool
     */
    private $hasCover = false;

    /**
     * Data path
     * @var string
     */
    private $path;

    /**
     * Rating
     * @var string
     */
    private $rating;

    /**
     * Comment
     * @var string
     */
    private $comment;

    /**
     * Last modified
     * @var \DateTime|null
     */
    private $lastModified;

    /**
     * Serie index
     * @var string
     */
    private $seriesIndex = 0;

    /**
     * ISBN
     * @var string
     */
    private $isbn;

    /**
     * LCCN
     * @var string
     */
    private $lccn;

    /**
     * Flags
     * @var bool
     */
    private $flags = false;

    /**
     * UUID
     * @var string
     */
    private $uuid;

    /**
     * A Cover object instance
     * @var Cover
     */
    private $cover;

    /**
     * A Serie object instance
     * @var Serie
     */
    private $serie;

    /**
     * Collection of author
     * @var AuthorCollection
     */
    private $authorCollection;

    /**
     * A tag collection instance
     * @var TagCollection
     */
    private $tagCollection;

    /**
     * An bookfile collection instance
     * @var BookFileCollection
     */
    private $bookFileCollection;

    /**
     * Date helper
     * @var \DateTime
     */
    private $dateHelper;

    /**
     * Constructor
     *
     * @param Cover              $cover
     * @param Serie              $serie
     * @param AuthorCollection   $authorCollection
     * @param TagCollection      $tagCollection
     * @param BookFileCollection $bookFileCollection
     * @param DateTime           $dateHelper
     */
    public function __construct(
        Cover $cover,
        Serie $serie,
        AuthorCollection $authorCollection,
        TagCollection $tagCollection,
        BookFileCollection $bookFileCollection,
        \DateTime $dateHelper
    ) {
        $this->cover = $cover;
        $this->serie = $serie;
        $this->authorCollection = $authorCollection;
        $this->tagCollection = $tagCollection;
        $this->bookFileCollection = $bookFileCollection;
        $this->dateHelper = $dateHelper;
    }

    /**
     * Find by id
     *
     * @param int $bookId
     *
     * @return $this
     *
     * @throws BookNotFoundException
     */
    public function findById($bookId)
    {
        $result = $this->getRepository()
            ->findById($bookId);

        if (empty($result[0])) {
            throw new BookNotFoundException(sprintf('Book width id %s not found', $bookId));
        }

        $this->setDataFromArray($result[0]);
        $this->authorCollection->findByBookId($bookId);
        $this->bookFileCollection->findFromBook($this);

        return $this;
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        if (!empty($this->id)) {
            throw new \LogicException('You cannot change existing ID');
        }

        $this->id = (int) $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pubdate
     *
     * @param string $pubdate
     * @param string $format
     *
     * @return $this
     */
    public function setPubdate($pubdate, $format = '!Y-m-d H:i:sP')
    {
        if ($pubDate = $this->dateHelper->createfromformat($format, $pubdate)) {
            $this->pubdate = $pubDate;
        }

        return $this;
    }

    /**
     * Get pubdate
     *
     * @return string
     */
    public function getPubdate()
    {
        return $this->pubdate;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sort
     *
     * @param string $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set cover present
     *
     * @param bool $coverPresent
     *
     * @return $this
     */
    public function setCoverPresent($coverPresent)
    {
        $this->hasCover = (bool) $coverPresent;

        return $this;
    }

    /**
     * Has Cover
     *
     * @return bool
     */
    public function hasCover()
    {
        return $this->hasCover;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set rating
     *
     * @param string $rating
     *
     * @return $this
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set lastModified
     *
     * @param string $lastModified
     *
     * @return $this
     */
    public function setLastModified($lastModified, $format = '!Y-m-d H:i:s#??????P')
    {
        if ($lastModified = $this->dateHelper->createfromformat($format, $lastModified)) {
            $this->lastModified = $lastModified;
        }

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set seriesIndex
     *
     * @param string $seriesIndex
     *
     * @return $this
     */
    public function setSeriesIndex($seriesIndex)
    {
        $this->seriesIndex = $seriesIndex;

        return $this;
    }

    /**
     * Get seriesIndex
     *
     * @return string
     */
    public function getSeriesIndex()
    {
        return $this->seriesIndex;
    }

    /**
     * Set isbn
     *
     * @param string $isbn
     *
     * @return $this
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set lccn
     *
     * @param string $lccn
     *
     * @return $this
     */
    public function setLccn($lccn)
    {
        $this->lccn = $lccn;

        return $this;
    }

    /**
     * Get lccn
     *
     * @return string
     */
    public function getLccn()
    {
        return $this->lccn;
    }

    /**
     * Set flags
     *
     * @param bool $flags
     *
     * @return $this
     */
    public function setFlags($flags)
    {
        $this->flags = (bool) $flags;

        return $this;
    }

    /**
     * Get flags
     *
     * @return bool
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return $this
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set authorCollection
     *
     * @param AuthorCollection $authorCollection
     *
     * @return $this
     */
    public function setAuthorCollection(AuthorCollection $authorCollection)
    {
        $this->authorCollection = $authorCollection;

        return $this;
    }

    /**
     * Get authorCollection
     *
     * @return AuthorCollection
     */
    public function getAuthors()
    {
        return $this->authorCollection;
    }

    /**
     * Set cover
     *
     * @param Cover $cover
     *
     * @return $this
     */
    public function setCover(Cover $cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return Cover
     */
    public function getCover()
    {
        if ($this->cover->getBookId() !== $this->getId()) {
            $this->cover->setBookInfo($this);
        }

        return $this->cover;
    }

    /**
     * Set serie
     *
     * @param Serie $serie
     *
     * @return $this
     */
    public function setSerie(Serie $serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set serie ID
     *
     * @param int $serieId
     *
     * @return $this
     */
    public function setSerieId($serieId)
    {
        $this->serie->setId($serieId);

        return $this;
    }

    /**
     * Set serie name
     *
     * @param string $serieName
     *
     * @return $this
     */
    public function setSerieName($serieName)
    {
        $this->serie->setName($serieName);

        return $this;
    }

    /**
     * Set serie sort
     *
     * @param string $serieSort
     *
     * @return $this
     */
    public function setSerieSort($serieSort)
    {
        $this->serie->setSort($serieSort);

        return $this;
    }

    /**
     * Set tagCollection
     *
     * @param TagCollection $tagCollection
     *
     * @return $this
     */
    public function setTagCollection(TagCollection $tagCollection)
    {
        $this->tagCollection = $tagCollection;

        return $this;
    }

    /**
     * Get tags
     *
     * @return TagCollection
     */
    public function getTags()
    {
        return $this->tagCollection;
    }

    /**
     * Set bookFileCollection
     *
     * @param BookFileCollection $bookFileCollection
     *
     * @return $this
     */
    public function setBookFileCollection(BookFileCollection $bookFileCollection)
    {
        $this->bookFileCollection = $bookFileCollection;

        return $this;
    }

    /**
     * Get specified file format
     *
     * @param  string $fileType
     *
     * @return \Cops\Model\BookFile\BookFileInterface
     */
    public function getFile($fileType = null)
    {
        return $this->bookFileCollection->findByFormatAndBookId($fileType, $this->getId());
    }

    /**
     * Get all files adapter
     *
     * @return BookFileCollection
     */
    public function getFiles()
    {
        return $this->bookFileCollection;
    }
}
