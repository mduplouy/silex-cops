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

use Cops\Model\Book;
use Cops\Model\Core;
use Cops\Exception\ImageProcessor\AdapterException;

/**
 * Cover model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Cover extends Core
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
    protected $storageDir;

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
     * @param Book   $book
     * @param string $sourceDir
     */
    public function __construct(Book $book, $sourceDir = null)
    {
        if ($sourceDir === null) {
            $sourceDir = BASE_DIR;
        }

        $this->storageDir = $sourceDir;
        $this->bookPath   = $book->getPath();
        $this->bookId     = $book->getId();

        if ($book->hasCover()) {
            $this->coverFile = sprintf($this->storageDir.'%s'.DS.'%s'.DS.'cover.jpg',
                $this->getConfig()->getValue('data_dir'),
                $this->bookPath
            );
        }
    }

    /**
     * Thumbnail path getter
     *
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function getThumbnailPath($width = null, $height = null)
    {
        if (is_null($this->coverFile) || !file_exists($this->coverFile)) {
            return false;
        }

        $this->setSize($width, $height);

        $this->thumbnailPath = sprintf(
            DS.'assets'.DS.'books'.DS.'%d'.DS.'%dx%d'.DS.'%d.jpg',
            substr($this->bookId, -1),
            $width,
            $height,
            $this->bookId
        );

        $this->thumbnailFile = BASE_DIR.$this->getConfig()->getValue('public_dir').$this->thumbnailPath;

        if (file_exists($this->thumbnailFile)) {
            return $this->thumbnailPath;
        } elseif (!file_exists($this->storageDir.$this->thumbnailPath)) {

            $targetDir = dirname($this->thumbnailFile);
            if (!is_dir($targetDir)) {
                mkdir(dirname($this->thumbnailFile), 0777, true);
            }

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
        $app = self::getApp();

        $app['image_processor']
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
            return (int) $this->getConfig()->getValue('cover_width');
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
            return (int) $this->getConfig()->getValue('cover_height');
        }
        return $this->height;
    }
}
