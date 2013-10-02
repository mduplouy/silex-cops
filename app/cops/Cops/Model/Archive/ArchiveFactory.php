<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Archive;

use Cops\Model\ArchiveAbstract;
use Cops\Exception\Archive\AdapterException;

/**
 * Archive factory
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ArchiveFactory extends ArchiveAbstract
{
    /**
     * Archive type keys
     */
    const TYPEKEY_ZIP   = 'ZIP';
    const TYPEKEY_TARGZ = 'TARGZ';

    /**
     * Archive types
     */
    const TYPE_ZIP    = 'Zip';
    const TYPE_TARGZ  = 'TarGz';

    /**
     * Archive types storage
     * @var array
     */
    protected $_instanceTypeStorage = array();

    /**
     * Instance type
     * @var string
     */
    private $_instanceType;

    /**
     * Archive type instance
     * @var array
     */
    private $_instance;

    /**
     * Constructor
     *
     * @param string $archiveType
     */
    public function __construct($archiveType=self::TYPEKEY_ZIP)
    {
        $this->_instanceTypeStorage[self::TYPEKEY_ZIP] = self::TYPE_ZIP;
        $this->_instanceTypeStorage[self::TYPEKEY_TARGZ]  = self::TYPE_TARGZ;

        $this->_instanceType = $archiveType;
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
                    'No model configured for the %s archive file format',
                    $this->_instanceType
                )
            );
        }

        if (!isset($this->_instance[$this->_instanceType])) {
            $className = '\\Cops\\Model\\Archive\\Adapter\\' .
                $this->_instanceTypeStorage[$this->_instanceType];

            $this->_instance[$this->_instanceType] = new $className;
        }
        return $this->_instance[$this->_instanceType];
    }

    /**
     * File types getter
     *
     * @return array
     */
    public function getArchiveTypes()
    {
        return $this->_instanceTypeStorage;
    }
}