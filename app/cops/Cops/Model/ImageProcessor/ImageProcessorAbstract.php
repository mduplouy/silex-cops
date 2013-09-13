<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\ImageProcessor;

use Cops\Model\ImageProcessor\ImageProcessorInterface;
use Cops\Model\Core;

/**
 * Image processing abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class ImageProcessorAbstract implements ImageProcessorInterface
{
    /**
     * Image width
     * @var int
     */
    protected $_width;

    /**
     * Image height
     * @var int
     */
    protected $_height;

    /**
     * Generate a thumbnail
     *
     * @param string $src Source file
     * @param string $dst Target file
     * @param array  $param Options
     */
    abstract public function generateThumbnail($src, $dst, array $params);

    /**
     * Width getter
     *
     * @return int
     */
    public function getWidth()
    {
        if (is_null($this->_width)) {
            $this->_width = (int) Core::getConfig()->getValue('cover_width');
        }
        return $this->_width;
    }

    /**
     * Width setter
     *
     * @param int $width
     *
     * @return Cops\Model\ImageProcessor\ImageProcessorInterface
     */
    public function setWidth($width)
    {
        $this->_width = (int) $_width;
        return $this;
    }

    /**
     * Height getter
     *
     * @return int
     */
    public function getHeight()
    {
        if (is_null($this->_height)) {
            $this->_height = (int) Core::getConfig()->getValue('cover_height');
        }
        return $this->_height;
    }

    /**
     * Height setter
     *
     * @param int $height
     *
     * @return Cops\Model\ImageProcessor\ImageProcessorInterface
     */
    public function setHeight($height)
    {
        $this->_height = $_height;
        return $this;
    }
}