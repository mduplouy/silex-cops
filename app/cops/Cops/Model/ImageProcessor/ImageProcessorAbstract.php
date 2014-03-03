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

use Silex\Application as BaseApplication;

/**
 * Image processing abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class ImageProcessorAbstract
{
    /**
     * Application instance
     * @var \Silex\Application
     */
    protected $app;

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
     * Constructor
     *
     * @param array $dataArray
     */
    public function __construct(BaseApplication $app, array $dataArray = array())
    {
        $this->app = $app;
    }

    /**
     * Width getter
     *
     * @return int
     */
    public function getWidth()
    {
        if (is_null($this->_width)) {
            $this->_width = (int) $this->app['config']->getValue('cover_width');
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
            $this->_height = (int) $this->app['config']->getValue('cover_height');
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