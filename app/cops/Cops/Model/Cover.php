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
     * Constructor
     *
     * @param Cops\Model\Book $book
     */
    public function __construct(Book $book)
    {
        if ($book->getHasCover()) {
            $this->_coverFile = sprintf(BASE_DIR.'%s'.DS.'%s'.DS.'cover.jpg',
                $this->getConfig()->getValue('data_dir'),
                $book->getPath()
            );

            $this->_thumbnailPath = sprintf(DS.'assets'.DS.'books'.DS.'%d'.DS.'%d.jpg',
                substr($book->getId(), -1),
                $book->getId()
            );

            $this->_thumbnailFile = BASE_DIR.$this->getConfig()->getValue('public_dir').$this->_thumbnailPath;
        }
    }

    /**
     * Thumbnail path getter
     *
     * @return string
     */
    public function getThumbnailPath()
    {
        if (file_exists($this->_thumbnailFile)) {
            return $this->_thumbnailPath;
        } elseif ($this->_coverFile && !file_exists(BASE_DIR.$this->_thumbnailPath)) {

            $targetDir = dirname($this->_thumbnailFile);
            if (!is_dir($targetDir)) {
                mkdir(dirname($this->_thumbnailFile), 0777, true);
            }

            $this->_generateThumbnail();
        }
        return $this->_thumbnailPath;
    }

    /**
     * Generate thumbnail
     *
     * @return void
     */
    protected function _generateThumbnail()
    {
        $processor = $this->getModel(
            'ImageProcessor\ImageProcessorFactory',
            $this->getConfig()->getValue('image_processor')
        )->getInstance();

        $processor->generateThumbnail($this->_coverFile, $this->_thumbnailFile);

    }

}