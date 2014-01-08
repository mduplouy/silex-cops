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
     * Constructor
     *
     * @param Cops\Model\Book $book
     */
    public function __construct(Book $book)
    {
        $this->bookPath = $book->getPath();
        $this->bookId = $book->getId();

        if ($book->hasCover()) {
            $this->coverFile = sprintf(BASE_DIR.'%s'.DS.'%s'.DS.'cover.jpg',
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
        if (is_null($this->coverFile)) {
            return false;
        }

        if (empty($width) || empty($height)) {
            $width = (int) $this->getConfig()->getValue('cover_width');
            $height = (int) $this->getConfig()->getValue('cover_height');
        }

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
        } elseif ($this->coverFile && !file_exists(BASE_DIR.$this->thumbnailPath)) {

            $targetDir = dirname($this->thumbnailFile);
            if (!is_dir($targetDir)) {
                mkdir(dirname($this->thumbnailFile), 0777, true);
            }

            $this->generateThumbnail($width, $height);
        }
        return $this->thumbnailPath;
    }

    /**
     * Generate thumbnail
     *
     * @return void
     */
    private function generateThumbnail($width, $height)
    {
        $app = self::getApp();

        $app['image_processor']
            ->setWidth($width)
            ->setHeight($height)
            ->generateThumbnail($this->coverFile, $this->thumbnailFile);
    }
}
