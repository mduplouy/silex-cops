<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

use Cops\Model\Core;
use Cops\Model\BookFile\BookFileFactory;

/**
 * Book model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Book extends Common
{
    /**
     * Object ID
     * @var int
     */
    protected $id;

    /**
     * Publication date
     * @var string
     */
    protected $pubdate;

    /**
     * Title
     * @var string
     */
    protected $title;

    /**
     * Has cover
     * @var bool
     */
    protected $hasCover;

    /**
     * Data path
     * @var string
     */
    protected $path;

    /**
     * Rating
     * @var string
     */
    protected $rating;

    /**
     * Comment
     * @var string
     */
    protected $comment;

    /**
     * Serie index
     * @var string
     */
    protected $seriesIndex;

    /**
     * An Author object instance
     * @var \Cops\Model\Author
     */
    protected $_author;

    /**
     * A Cover object instance
     * @var \Cops\Model\Cover
     */
    protected $_cover;

    /**
     * A Serie object instance
     * @var \Cops\Model\Serie
     */
    protected $_serie;

    /**
     * An array of file adapter instance
     * @var array
     */
    protected $_file = array();

    /**
     * Get the latest added books
     *
     * @return \Cops\Model\Collection  Collection of Book
     */
    public function getLatest()
    {
        return $this->getResource()->getLatestCollection($this);
    }

    /**
     * Load book
     *
     * @param int $bookId
     *
     * @return \Cops\Model\Book
     */
    public function load($bookId)
    {
        return $this->getResource()->load($bookId, $this);
    }

    /**
     * Has cover
     *
     * @return bool
     */
    public function hasCover()
    {
        return (bool) $this->hasCover;
    }

    /**
     * Cover object getter
     *
     * @return \Cops\Model\Cover
     */
    public function getCover()
    {
        if (is_null($this->_cover)) {
            $this->_cover = $this->getModel('Cover', $this);
        }
        return $this->_cover;
    }

    /**
     * Serie object getter
     *
     * @return \Cops\Model\Serie
     */
    public function getSerie()
    {
        if (is_null($this->_serie)) {
            $this->_serie = $this->getModel('Serie');
        }
        return $this->_serie;
    }

    /**
     * Author object getter
     *
     * @return \Cops\Model\Author
     */
    public function getAuthor()
    {
        if (is_null($this->_author)) {
            $this->_author = $this->getModel('Author');
        }
        return $this->_author;
    }

    /**
     * File adapter getter
     *
     * @return \Cops\Model\Book\FileInterface
     */
    public function getFile($fileType=BookFileFactory::TYPE_EPUB)
    {
        if (!isset($this->_file[$fileType])) {
            $this->_file[$fileType] = $this->getModel('BookFile\\BookFileFactory', $fileType)
                ->getInstance();
        }
        return $this->_file[$fileType];
    }

    /**
     * Get all files adapter
     *
     * @return array An array of fileInterface instance
     */
    public function getFiles()
    {
        return $this->_file;
    }

    /**
     * Get other books from author
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getOtherBooksFromAuthor()
    {
        return $this->getResource()->getOtherBooksFromAuthor($this->getAuthor()->getId(), $this);
    }

    /**
     * Get other books from serie
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getOtherBooksFromSerie()
    {
        return $this->getResource()->getOtherBooksFromSerie($this->getSerie()->getId(), $this);
    }

    /**
     * Get collection from serie ID
     *
     * @param int $serieId
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getCollectionBySerieId($serieId)
    {
        return $this->getResource()->loadBySerieId($serieId, $this);
    }

    /**
     * Get collection from author ID
     *
     * @param int $authorId
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getCollectionByAuthorId($authorId)
    {
        return $this->getResource()->loadByAuthorId($authorId, $this);
    }

    /**
     * Get collection from tag ID
     *
     * @param int $tagId
     *
     * @return \Cops\Model\Book\Collection
     */
    public function getCollectionByTagId($tagId)
    {
        return $this->getResource()->loadByTagId($tagId, $this);
    }

    /**
     * Empty properties on clone
     */
    public function __clone()
    {
        $this->id          = null;
        $this->pubdate     = null;
        $this->hasCover    = null;
        $this->path        = null;
        $this->rating      = null;
        $this->comment     = null;
        $this->seriesIndex = null;
        $this->_serie      = null;
        $this->_author     = null;
        $this->_cover      = null;
        $this->_file       = array();
        parent::__clone();
    }
}
