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
 * Image processor factory
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ImageProcessorFactory
{
    /**
     * Processor instance
     * @var \Cops\Model\Image\ImageProcessorInterface
     */
    protected $_instance;

    /**
     * Constructor
     *
     * @param string $processingType
     */
    public function __construct($processingType='gd')
    {
        $this->_processingType = $processingType;
    }

    /**
     *
     */
    public function getInstance()
    {
        if (isset($this->_instance)) {
            return $this->_instance;
        }

        $app = Core::getApp();
        $this->_instance = $app['image_'.$this->_processingType];
        return $this->_instance;
    }
}