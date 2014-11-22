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
use Cops\Model\Book;
use Cops\Exception\ImageProcessor\AdapterException;
use Silex\Application as BaseApplication;

/**
 * Cover model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Cover extends EntityAbstract
{
    /**
     * Cover file
     * @var string
     */
    protected $coverFile;

    /**
     * Thumbnail path
     * @var string
     */
    protected $thumbnailPath;

    /**
     * Thumbnail file
     * @var string
     */
    protected $thumbnailFile;

    /**
     * Book path
     * @var string
     */
    private $bookPath;

    /**
     * Book ID
     * @var int
     */
    private $bookId;

    /**
     * Storage directory
     * @var string
     */
    protected $storageDir = BASE_DIR;

    /**
     * Width
     * @var int
     */
    private $width;

    /**
     * Height
     * @var int
     */
    private $height;

    /**
     * Constructor
     *
     * @param array $dataArray
     *
     * @return \Cops\Model\Core
     */
    public function __construct(BaseApplication $app, array $dataArray = array())
    {
        $this->app = $app;
        $this->setData($dataArray);
    }

    /**
     * Book setter
     *
     * @param Book
     *
     * @return $this
     */
    public function setBook(Book $book)
    {
        $this->bookPath   = $book->getPath();
        $this->bookId     = $book->getId();

        if ($book->hasCover()) {
            $this->coverFile = sprintf('%s'.DS.'%s'.DS.'cover.jpg',
                $this->app['config']->getDatabasePath(),
                $this->bookPath
            );
        }
        return $this;
    }

    /**
     * Thumbnail path getter
     *
     * @param int $width
     * @param int $height
     *
     * @return false|string
     */
    public function getThumbnailPath($width = null, $height = null)
    {
        if (is_null($this->coverFile) || !file_exists($this->coverFile)) {
            return false;
        }

        $this->setSize($width, $height);

        $this->thumbnailPath = sprintf(
            DS.'assets'.DS.'books'.DS.'%s'.DS.'%d'.DS.'%dx%d'.DS.'%d.jpg',
            $this->app['config']->getValue('current_database_key'),
            substr($this->bookId, -1),
            $this->getWidth(),
            $this->getHeight(),
            $this->bookId
        );

        $this->thumbnailFile = BASE_DIR.$this->app['config']->getValue('public_dir').$this->thumbnailPath;

        if (!file_exists($this->thumbnailFile)) {
            $this->generateThumbnail();
        }

        return $this->thumbnailPath;
    }

    /**
     * Generate thumbnail
     *
     * @return void
     */
    private function generateThumbnail()
    {
        $targetDir = dirname($this->thumbnailFile);
        if (!is_dir($targetDir)) {
            mkdir(dirname($this->thumbnailFile), 0777, true);
        }

        $this->app['factory.image']
            ->getInstance($this->app['config']->getValue('image_processor'))
            ->setWidth($this->getWidth())
            ->setHeight($this->getHeight())
            ->generateThumbnail($this->coverFile, $this->thumbnailFile);
    }

    /**
     * Size setter
     *
     * @param int $width
     * @param int $height
     *
     * @return Cover
     */
    public function setSize($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Width getter
     *
     * @return int
     */
    private function getWidth()
    {
        if ($this->width === null || $this->height === null) {
            return (int) $this->app['config']->getValue('cover_width');
        }
        return $this->width;
    }

    /**
     * Height getter
     *
     * @return int
     */
    private function getHeight()
    {
        if ($this->width === null || $this->height === null) {
            return (int) $this->app['config']->getValue('cover_height');
        }
        return $this->height;
    }
}
