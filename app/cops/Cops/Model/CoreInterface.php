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
 * Core interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface CoreInterface
{
    /**
     * Standard object loader
     *
     * @param string $className The classname
     * @param array  $args Constructor args
     */
    public function getModel($className, $args = array());

    /**
     * Resource object loader
     *
     * @return \Cops\Model\Resource
     */
    public function getResource();

}
