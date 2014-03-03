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

use Silex\Application as BaseApplication;
use Cops\Model\EntityAbstract;
use Cops\Model\BookFile\BookFileFactory;

/**
 * Book model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Book extends EntityAbstract
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
    protected $authorCollection;

    /**
     * A Cover object instance
     * @var \Cops\Model\Cover
     */
    protected $cover;

    /**
     * A Serie object instance
     * @var \Cops\Model\Serie
     */
    protected $serie;

    /**
     * A tag collection instance
     * @var \Cops\Model\Tag\Collection
     */
    protected $tagCollection;

    /**
     * An bookfile collection instance
     * @var \Cops\Model\BookFile\Collection
     */
    protected $bookFileCollection;

    /**
     * Constructor
     *
     * @param array $dataArray
     *
     * @return \Cops\Model\Core
     */
    public function __construct(BaseApplication $app, array $dataArray = array())
    {
        parent::__construct($app, $dataArray);
        $this->cover                = $this->app['model.cover'];
        return $this->setDefaultProperties();
    }

    /**
     * Set data into object
     *
     * @param array
     *
     * @return \Cops\Model\Core
     */
    public function setData(array $dataArray)
    {
        parent::setData($dataArray);
        return $this->setDefaultProperties();
    }

    /**
     * Set default properties as object from DIC
     *
     * @return $this
     */
    private function setDefaultProperties()
    {
        $this->authorCollection     = $this->app['model.author']->getCollection();
        $this->serie                = $this->app['model.serie']->setBook($this);
        $this->tagCollection        = $this->app['model.tag']->getCollection();
        $this->bookFileCollection   = $this->app['model.bookfile']->getCollection();
        return $this;
    }

    /**
     * Load one book data
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
        $this->authorCollection = $this->authorCollection->getByBookId($this->getId());

        // Set serie data
        if (!empty($result['serie_id'])) {
            $this->serie->setData(array(
                'id'   => $result['serie_id'],
                'name' => $result['serie_name'],
                'sort' => $result['serie_sort'],
            ));
        }

        // Set bookfile
        $this->bookFileCollection->getFromBook($this);

        // Set tags
        $this->tagCollection->getByBookId($this->getId());

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
     * @return Cover
     */
    public function getCover()
    {
        return $this->cover->setBook($this);
    }

    /**
     * Serie object getter
     *
     * @return \Cops\Model\Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Author collection getter
     *
     * @return \Cops\Model\Author\Collection
     */
    public function getAuthors()
    {
        return $this->authorCollection;
    }

    /**
     * File adapter getter
     *
     * @param  string $fileType
     *
     * @return \Cops\Model\BookFile\BookFileInterface
     */
    public function getFile($fileType = BookFileFactory::TYPE_EPUB)
    {
        return $this->bookFileCollection->findFormat($fileType, $this->getId());
    }

    /**
     * Get all files adapter
     *
     * @return array An array of fileInterface instance
     */
    public function getFiles()
    {
        return $this->bookFileCollection;
    }

    /**
     * Tag getter
     *
     * @return Collection A tag collection (can be empty)
     */
    public function getTags()
    {
        return $this->tagCollection;
    }

    /**
     * Update book author
     *
     * @param string|array $authors
     * @param int          $bookId
     *
     * @return bool
     *
     * @todo inject author collection here instead of array
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
        parent::__clone();
        $this->id                 = null;
        $this->pubdate            = null;
        $this->hasCover           = null;
        $this->path               = null;
        $this->rating             = null;
        $this->comment            = null;
        $this->seriesIndex        = null;

        $this->setDefaultProperties();
    }
}
