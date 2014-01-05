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

use Cops\Exception\ImageProcessor\AdapterException;

/**
 * Image processor factory
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ImageProcessorFactory
{
    /**
     * Instance types
     */
    const TYPE_GD       = 'gd';
    const TYPE_IMAGICK  = 'imagick';

    /**
     * Instance type storage
     * @var array
     */
    private $_instanceTypeStorage;

    /**
     * Instance type
     * @var string
     */
    private $_instanceType;

    /**
     * Processor instance
     * @var array
     */
    protected $_instance;

    /**
     * Constructor
     *
     * @param string $processingType
     */
    public function __construct($processingType = self::TYPE_GD)
    {
        $this->_instanceType = $processingType;

        $this->_instanceTypeStorage = array(
            self::TYPE_GD      => self::TYPE_GD,
            self::TYPE_IMAGICK => self::TYPE_IMAGICK,
        );
    }

    /**
     * Instance getter
     *
     * @return ImageProcessorInterface
     */
    public function getInstance()
    {
        if (!isset($this->_instanceTypeStorage[$this->_instanceType])) {
            throw new AdapterException(
                sprintf(
                    'No model configured for the %s image processor',
                    $this->_instanceType
                )
            );
        }

        if (!isset($this->_instance[$this->_instanceType])) {
            $className = __NAMESPACE__.'\\Adapter\\'.ucfirst($this->_instanceType);
            $this->_instance[$this->_instanceType] = new $className;
        }
        return $this->_instance[$this->_instanceType];
    }
}