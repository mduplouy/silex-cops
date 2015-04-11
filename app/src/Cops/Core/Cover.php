<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core;

use Cops\Core\Config;
use Cops\Core\Image\ImageFactory;
use Cops\Core\Entity\Book;

/**
 * Cover
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Cover
{
    /**
     * Config instance
     * @var Config
     */
    protected $config;

    /**
     * ImageFactory instance
     * @var ImageFactory
     */
    protected $imageFactory;

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
    protected $bookPath;

    /**
     * Book ID
     * @var int
     */
    protected $bookId;

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
     * @param Config       $config
     * @param ImageFactory $imageFactory
     */
    public function __construct(Config $config, ImageFactory $imageFactory)
    {
        $this->config       = $config;
        $this->imageFactory = $imageFactory;
    }

    /**
     * Book info setter
     *
     * @param Book
     *
     * @return $this
     */
    public function setBookInfo(Book $book)
    {
        $this->bookPath   = $book->getPath();
        $this->bookId     = $book->getId();

        if ($book->hasCover()) {
            $this->coverFile = sprintf('%s'.DS.'%s'.DS.'cover.jpg',
                $this->config->getDatabasePath(),
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
            $this->config->getValue('current_database_key'),
            substr($this->bookId, -1),
            $this->getWidth(),
            $this->getHeight(),
            $this->bookId
        );

        $this->thumbnailFile = BASE_DIR.$this->config->getValue('public_dir').$this->thumbnailPath;

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

        $this->imageFactory->getInstance($this->config->getValue('image_processor'))
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
     * @return $this
     */
    public function setSize($width, $height)
    {
        $this->width  = (int) $width;
        $this->height = (int) $height;

        return $this;
    }

    /**
     * Width getter
     *
     * @return int
     */
    private function getWidth()
    {
        $width = $this->width;
        if ($this->width === null || $this->height === null) {
            $width = (int) $this->config->getValue('cover_width');
        }

        return $width;
    }

    /**
     * Height getter
     *
     * @return int
     */
    private function getHeight()
    {
        $height = $this->height;
        if ($this->width === null || $this->height === null) {
            $height = (int) $this->config->getValue('cover_height');
        }

        return $height;
    }

    /**
     * Get bookId
     *
     * @return int
     */
    public function getBookId()
    {
        return $this->bookId;
    }
}
