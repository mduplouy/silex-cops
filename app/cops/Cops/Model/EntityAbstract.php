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

use Cops\Model\EntityInterface;
use Silex\Application as BaseApplication;

/**
 * Entity abstract class model
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class EntityAbstract implements EntityInterface
{
    /**
     * Application instance
     * @var Application
     */
    protected $app;

    /**
     * Resource instance
     * @var \Cops\Model\ResourceAbstract
     */
    protected $resource;

    /**
     * Constructor
     *
     * @param array $dataArray
     *
     * @return \Cops\Model\Core
     */
    public function __construct(BaseApplication $app, array $dataArray = array())
    {
        $this->app = $app;

        if (is_null($this->resource)) {
            $className = get_called_class();
            $resourceClassName = sprintf('%s\\Resource', $className);
            $this->resource = new $resourceClassName($app, $this);
        }

        return $this->setData($dataArray);
    }

    /**
     * Set/Get attribute wrapper
     * @todo Remove this and code any needed setter / getter
     *
     * @param   string $method
     * @param   array $args
     *
     * @return  mixed
     */
    public function __call($method, $args)
    {
        $propKey = $this->getPropertyName(substr($method, 3));

        switch (substr($method, 0, 3)) {
            case 'get' :
                if (property_exists($this, $propKey)) {
                    return $this->$propKey;
                } else {
                    return null;
                }

            case 'set' :
                return $this->setData(array($propKey => $args[0]));
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
    public function setData(array $dataArray)
    {
        foreach ($dataArray as $prop => $value) {
            $prop = $this->getPropertyName($prop);
            $setterName =  'set'.ucfirst($prop);
            if (method_exists($this, $setterName)) {
                $this->{$setterName}($value);
            } elseif (property_exists($this, $prop)) {
                $this->$prop = $value;
            }
        }
        return $this;
    }

    /**
     * Resource object loader
     *
     * @return \Cops\Model\ResourceAbstract
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Collection object loader
     *
     * @return \Cops\Model\Collection
     */
    public function getCollection()
    {
        $fullClassName = get_called_class().'\\Collection';
        if (!class_exists($fullClassName)) {
            throw new \RuntimeException(
                sprintf('Requested collection %s does not exists', $fullClassName)
            );
        }
        return new $fullClassName($this);
    }

    /**
     * Application getter
     *
     * @return \Silex\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Get the data key from a "flat" property name
     *
     * @param string $prop
     *
     * @return string
     */
    protected function getPropertyName($prop)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $prop))));
    }

    /**
     * Get the download-safe name of an entity
     *
     * @return string
     */
    public function getDownloadSafeName()
    {
        return $this->app['utils']->removeAccents($this->getName());
    }

    /**
     * Empty properties on clone
     */
    public function __clone()
    {
        $this->resource->setEntity($this);
    }
}