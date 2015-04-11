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

/**
 * Factory interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface FactoryInterface
{
    /**
     * Constructor
     *
     * @param array $adapters
     */
    public function __construct(array $adapters);

    /**
     * Instance getter
     *
     * @param string $instance
     *
     * @return mixed
     */
    public function getInstance($instance = null);
}
