<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\BookFile;

use Cops\Model\Core;
use Cops\Exception\BookFile\AdapterException;

/**
 * Book file factory
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookFileFactory
{
    /**
     * File types
     */
    const TYPE_EPUB = 'EPUB';
    const TYPE_PDF  = 'PDF';
    const TYPE_MOBI = 'MOBI';

    /**
     * File types storage
     * @var array
     */
    protected $_instanceTypeStorage = array();

    /**
     * Instance type
     * @var string
     */
    private $_instanceType;

    /**
     * File type instance
     * @var array
     */
    private $_instance;

    /**
     * Constructor
     */
    public function __construct($fileType=self::TYPE_EPUB)
    {
        $this->_instanceTypeStorage[self::TYPE_EPUB] = self::TYPE_EPUB;
        $this->_instanceTypeStorage[self::TYPE_PDF]  = self::TYPE_PDF;
        $this->_instanceTypeStorage[self::TYPE_MOBI] = self::TYPE_MOBI;

        $this->_instanceType = $fileType;
    }

    /**
     * Instance getter
     *
     * @return \Cops\Model\Image\ImageProcessorInterface
     */
    public function getInstance()
    {
        if (!isset($this->_instanceTypeStorage[$this->_instanceType])) {
            throw new AdapterException(
                sprintf(
                    'No model configured for the %s book file format',
                    $this->_instanceType
                )
            );
        }

        if (!isset($this->_instance[$this->_instanceType])) {
            $className = __NAMESPACE__.'\\Adapter\\' .
                ucfirst(strtolower($this->_instanceType));

            $this->_instance[$this->_instanceType] = new $className;
        }
        return $this->_instance[$this->_instanceType];
    }

    /**
     * File types getter
     *
     * @return array
     */
    public function getFileTypes()
    {
        return $this->_instanceTypeStorage;
    }
}