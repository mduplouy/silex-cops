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

interface FactoryInterface
{
    /**
     * Instance getter for factory
     *
     * @param  string $instance
     *
     * @return \Cops\Model\FactoryInterface
     */
    public function getInstance($instance);

}
