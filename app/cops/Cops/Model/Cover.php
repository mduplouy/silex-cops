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
    protected $_coverFile;

    /**
     * Thumbnail path
     * @var string
     */
    protected $_thumbnailPath;

    /**
     * Thumbnail file
     * @var string
     */
    protected $_thumbnailFile;

    /**
     * Book path
     * @var string
     */
    private $_bookPath;

    /**
     * Book ID
     * @var int
     */
    private $_bookId;

    /**
     * Constructor
     *
     * @param Cops\Model\Book $book
     */
    public function __construct(Book $book)
    {
        $this->_bookPath = $book->getPath();
        $this->_bookId = $book->getId();

        if ($book->getHasCover()) {
            $this->_coverFile = sprintf(BASE_DIR.'%s'.DS.'%s'.DS.'cover.jpg',
                $this->getConfig()->getValue('data_dir'),
                $this->_bookPath
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
    public function getThumbnailPath($width=null, $height=null)
    {
        if (empty($width) || empty($height)) {
            $width = (int) $this->getConfig()->getValue('cover_width');
            $height = (int) $this->getConfig()->getValue('cover_height');
        }

        $this->_thumbnailPath = sprintf(DS.'assets'.DS.'books'.DS.'%d'.DS.'%dx%d'.DS.'%d.jpg',
            substr($this->_bookId, -1),
            $width,
            $height,
            $this->_bookId
        );

        $this->_thumbnailFile = BASE_DIR.$this->getConfig()->getValue('public_dir').$this->_thumbnailPath;

        if (file_exists($this->_thumbnailFile)) {
            return $this->_thumbnailPath;
        } elseif ($this->_coverFile && !file_exists(BASE_DIR.$this->_thumbnailPath)) {

            $targetDir = dirname($this->_thumbnailFile);
            if (!is_dir($targetDir)) {
                mkdir(dirname($this->_thumbnailFile), 0777, true);
            }

            $this->_generateThumbnail($width, $height);
        }
        return $this->_thumbnailPath;
    }

    /**
     * Generate thumbnail
     *
     * @return void
     */
    protected function _generateThumbnail($width, $height)
    {
        $processor = $this->getModel(
            'ImageProcessor\ImageProcessorFactory',
            $this->getConfig()->getValue('image_processor')
        )->getInstance();

        $processor
            ->setWidth($width)
            ->setHeight($height)
            ->generateThumbnail($this->_coverFile, $this->_thumbnailFile);
    }

}
