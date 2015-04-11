<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core;

use Cops\Core\AbstractApplicationAware;
use Cops\Core\UseRepositoryInterface;

/**
 * Entity abstract class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractEntity extends AbstractApplicationAware implements UseRepositoryInterface
{
    /**
     * Repository closure
     * @var \Closure
     */
    private $repositoryClosure;

    /**
     * Repository instance
     * @var \Cops\Core\RepositoryInterface
     */
    private $repository;

    /**
     * Set data from array into object
     *
     * @param array $data
     *
     * @return self
     */
    public function setDataFromArray(array $data)
    {
        foreach ($data as $property => $value) {

            $propertyName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property))));

            $setterName =  'set'.ucfirst($propertyName);
            if (method_exists($this, $setterName)) {
                $this->{$setterName}($value);
            }
        }

        return $this;
    }

    /**
     * @inherit
     */
    public function setRepositoryClosure(\Closure $closure)
    {
        $this->repositoryClosure = $closure;

        return $this;
    }

    /**
     * @inherit
     */
    public function getRepositoryClosure()
    {
        if (null === $this->repositoryClosure) {
            throw new \LogicException('Repository closure must be set prior to get');
        }

        return $this->repositoryClosure;
    }

    /**
     * Set repository
     *
     * @param RepositoryInterface
     *
     * @return self
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @inherit
     */
    public function getRepository()
    {
        if (null === $this->repository) {
            $this->repository = $this->getRepositoryClosure()->__invoke();
        }

        return $this->repository;
    }
}
