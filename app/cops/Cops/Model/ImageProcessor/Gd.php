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
use Cops\Model\ImageProcessor\ImageProcessorInterface;

/**
 * GD Image processing class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Gd implements ImageProcessorInterface
{

    protected $_width;

    protected $_height;

    /**
     * Image resource
     * @var \resource
     */
    protected $_image;

    /**
     * Generate a thumbnail for image
     *
     * @param string $src The source image file path
     * @param string $dest The target image file path
     * @param array $params Options
     */
    public function generateThumbnail($src, $dest, array $params=array())
    {
        $sourceInfo = getimagesize($src);

        if (isset($params['width'])) {
            $this->setWidth($params['width']);
        }
        if (isset($params['height'])) {
            $this->setHeight($params['height']);
        }

        $sourceImage = imagecreatefromjpeg($src);

        $targetImage = imagecreatetruecolor($this->getWidth(), $this->getHeight());

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $this->getWidth(),
            $this->getHeight(),
            $sourceInfo[0],
            $sourceInfo[1]
        );

        imagejpeg($targetImage, $dest, 80);
    }

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


    public function resize($width=null, $height=null)
    {
        return $this;
    }
}