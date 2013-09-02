<?php

namespace Cops\Model;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Common purpose class model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Common
{
    /**
     * Model object instance registry
     * @var array
     */
    protected $_modelInstance = array();

    /**
     * Resource access
     * @var mixed
     */
    protected $_resource;

    /**
     * Simple object loader
     *
     * @param string $className
     * @return mixed
     */
    public function getModel($className)
    {
        if (!isset($this->_objecInstance[$className])) {
            $fullClassName = '\\Cops\\Model\\'.$className;
            $this->_objecInstance[$className] = new $fullClassName();
        }
        return $this->_objecInstance[$className];
    }

}