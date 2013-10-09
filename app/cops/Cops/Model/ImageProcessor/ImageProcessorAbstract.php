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

use Cops\Model\Core;

/**
 * Image processing abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class ImageProcessorAbstract
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
        $this->_width = (int) $width;
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
        $this->_height = $height;
        return $this;
    }
}