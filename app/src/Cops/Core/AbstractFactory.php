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

use Cops\Core\AdapterNotFoundException;

/**
 * Abstract factory
 * @author <mathieu.duplouy@gmail.com>
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * Adapters
     * @var array
     */
    private $adapters = array();

    /**
     * Constructor
     *
     * @param array $adapters
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(array $adapters)
    {
        foreach ($adapters as $key => $type) {
            if (!$type instanceof \Closure) {
                throw new \UnexpectedValueException('Adapters must be provided as array of \Closure');
            }
        }

        $this->adapters = $adapters;
    }

    /**
     * Instance getter
     *
     * @param string $instance
     *
     * @return mixed
     *
     * @throws AdapterNotFoundException
     */
    public function getInstance($instance = null)
    {
        if (!array_key_exists($instance, $this->adapters)) {
            throw new AdapterNotFoundException(sprintf(
                'Adapter %s does not exist, valid adapters are %s',
                $instance,
                implode(', ', array_keys($this->adapters))
            ));
        }

        return $this->adapters[$instance]->__invoke();
    }

    /**
     * Get adapter keys
     *
     * @return array
     */
    public function getAdapterKeys()
    {
        return array_keys($this->adapters);
    }
}
