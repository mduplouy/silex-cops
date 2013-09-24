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
abstract class Common extends Core
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
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }

        $propKey = $this->_getPropNameFromMethod($method);

        switch (substr($method, 0, 3)) {
            case 'get' :
                if (property_exists($this, $propKey)) {
                    return $this->$propKey;
                } else {
                    return null;
                }

            case 'set' :
                $this->$propKey = $args[0];
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
            if (property_exists($this, $prop)) {
                $this->$prop = $value;
            }
        }
        return $this;
    }

    /**
     * Get the property name from setter method name
     *
     * @param string $method
     *
     * @return string
     */
    protected function _getPropNameFromMethod($method)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', substr($method, 3)))));
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
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $prop))));
    }
}