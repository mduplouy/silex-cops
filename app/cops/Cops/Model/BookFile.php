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

use Cops\Model\EntityAbstract;
use Silex\Application as BaseApplication;
use Cops\Model\Book;
use Cops\Model\Book\Collection as BookCollection;

/**
 * Book file abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookFile extends EntityAbstract
{
    /**
     * Bookfile ID
     * @var int
     */
    protected $id;

    /**
     * Book ID
     * @var int
     */
    protected $bookId;

    /**
     * Bookfile format
     *
     * @var string
     */
    protected $format;

    /**
     * File size in bytes
     *
     * @var int
     */
    protected $uncompressedSize = 0;

    /**
     * File name without extension
     *
     * @var string
     */
    protected $name;

    /**
     * Bookfile directory
     *
     * @var string
     */
    protected $directory;

    /**
     * Storage directory
     *
     * @var string
     */
    protected $storageDir;

    /**
     * Constructor
     *
     * @param Silex\Application $app
     * @param array             $dataArray
     */
    public function __construct(BaseApplication $app, array $dataArray = array())
    {
        $resourceClassName = sprintf('%s\\Resource', __CLASS__);
        $this->resource = new $resourceClassName($app, $this);

        $this->storageDir = $app['config']->getDatabasePath();

        return parent::__construct($app, $dataArray);
    }

    /**
     * Add book files to a book collection
     *
     * @param  \Cops\Model\Book\Collection $collection
     *
     * @return \Cops\Model\Book\Collection
     */
    public function populateBookCollection(BookCollection $collection)
    {
        return $this->getResource()->populateBookCollection($collection);
    }

    /**
     * Reset data on cloning
     */
    public function __clone()
    {
        parent::__clone();
        $this->id               = null;
        $this->bookId           = null;
        $this->format           = null;
        $this->uncompressedSize = 0;
        $this->name             = null;
        $this->directory        = null;
    }
}
