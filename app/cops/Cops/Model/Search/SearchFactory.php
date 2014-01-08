<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Search;

use Cops\Model\Core;
use Cops\Model\Book;
use Cops\Model\ImageProcessor\SearchInterface;
use Cops\Exception\Search\AdapterException;

/**
 * Image processor factory
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SearchFactory extends Core
{
    /**
     * Instance types
     */
    const TYPE_SQLITE = 'sqlite';

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
     * @param string $searchType
     */
    public function __construct($searchType='sqlite')
    {
        $this->_instanceType = $searchType;

        $this->_instanceTypeStorage = array(
            self::TYPE_SQLITE      => self::TYPE_SQLITE,
        );
    }

    /**
     * Instance getter
     *
     * @return \Cops\Model\Image\SearchInterface
     */
    public function getInstance()
    {
        if (!isset($this->_instanceTypeStorage[$this->_instanceType])) {
            throw new AdapterException(
                sprintf(
                    'No model configured for the %s search engine',
                    $this->_instanceType
                )
            );
        }

        if (!isset($this->_instance[$this->_instanceType])) {
            $className = 'Search\\Adapter\\'.ucfirst($this->_instanceType);
            $this->_instance[$this->_instanceType] = $this->getModel($className);
        }
        return $this->_instance[$this->_instanceType];
    }
}