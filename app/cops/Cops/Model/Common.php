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

/**
 * Common class model
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Common extends Core
{
    /**
     * Data storage
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     *
     * @param array $dataArray
     *
     * @return \Cops\Model\Core
     */
    public function __construct($dataArray=null)
    {
        if (is_array($dataArray)) {
            $this->setData($dataArray);
        }
        return $this;
    }

    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     *
     * @return  mixed
     */
    public function __call($method, $args)
    {
        $dataKey = $this->_getDataKeyFromMethod($method);

        switch (substr($method, 0, 3)) {
            case 'get' :
                if (array_key_exists($dataKey, $this->_data)) {
                    return $this->_data[$dataKey];
                } else {
                    return null;
                }

            case 'set' :
                $this->_data[$dataKey] = $args[0];
                return $this;
        }
        throw new \Exception('Invalid method name : '.get_called_class().'::'.$method);
    }

    /**
     * Set data into object
     *
     * @param array
     *
     * @return \Cops\Model\Core
     */
    public function setData($dataArray)
    {
        foreach($dataArray as $prop => $value) {
            $prop = $this->_getDataKeyFromProperty($prop);
            $this->_data[$prop] = $value;
        }
        return $this;
    }

    /**
     * Get the data key from setter method name
     *
     * @param string $method
     *
     * @return string
     */
    protected function _getDataKeyFromMethod($method)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', substr($method, 3))));
    }

    /**
     * Get the data key from a "flat" property name
     *
     * @param string $prop
     *
     * @return string
     */
    protected function _getDataKeyFromProperty($prop)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $prop)));
    }
}