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
     * Collection of author
     * @var \Cops\Model\Author\Collection
     */
    protected $_authors;

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
     * A tag collection instance
     * @var \Cops\Model\Tag\Collection
     */
    protected $tags;

    /**
     * An array of file adapter instance
     * @var array
     */
    protected $_files = array();

    /**
     * Load book
     *
     * @param int $bookId
     *
     * @return \Cops\Model\Book
     */
    public function load($bookId)
    {
        $result = $this->getResource()->load($bookId);

        $this->setData($result);

        // Set author collection
        $this->_authors = $this->getModel('Author')->getCollection()->getByBookId($this->getId());

        // Set serie data
        if (!empty($result['serie_id'])) {
            $this->getSerie()->setData(array(
                'id'   => $result['serie_id'],
                'name' => $result['serie_name'],
                'sort' => $result['serie_sort'],
            ));
        }

        // Set bookfile data
        // @TODO, change this
        $this->getModel('BookFile')->loadFromBook($this);

        return $this;
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
     * @param string $storageDir
     *
     * @return Cover
     */
    public function getCover($storageDir = null)
    {
        if ($this->_cover === null) {
            $this->_cover = $this->getModel('Cover', array($this, $storageDir));
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
     * Author collection getter
     *
     * @return \Cops\Model\Author\Collection
     */
    public function getAuthors()
    {
        if (is_null($this->_authors)) {
            $this->_authors = $this->getModel('Author')
                ->getCollection()
                ->getByBookId($this->getId());
        }
        return $this->_authors;
    }

    /**
     * File adapter getter
     *
     * @return \Cops\Model\BookFile\BookFileInterface
     */
    public function getFile($fileType = BookFileFactory::TYPE_EPUB)
    {
        if (!isset($this->_files[$fileType])) {
            $this->_files[$fileType] = $this->getModel('BookFile\\BookFileFactory', $fileType)
                ->getInstance();
        }
        return $this->_files[$fileType];
    }

    /**
     * Get all files adapter
     *
     * @return array An array of fileInterface instance
     */
    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * Tag getter
     *
     * @return Collection A tag collection (can be empty)
     */
    public function getTags()
    {
        return $this->getModel('Tag')
            ->getCollection()
            ->getByBookId($this->getId());
    }

    /**
     * Update book author
     *
     * @param string|array $authors
     * @param int          $bookId
     *
     * @return bool
     */
    public function updateAuthor($authors, $bookId = null)
    {
        if ($bookId === null) {
            $bookId = $this->getId();
        }
        if (!is_array($authors)) {
            $authors = explode('&', $authors);
        }
        return $this->getResource()->updateAuthor($bookId, $authors);
    }

    /**
     * Update book title
     *
     * @param string $title
     * @param int    $bookId
     *
     * @return bool
     */
    public function updateTitle($title, $bookId = null)
    {
        if ($bookId === null) {
            $bookId = $this->getId();
        }
        return $this->getResource()->updateTitle($bookId, $title);
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
