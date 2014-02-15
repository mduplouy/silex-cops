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

use Cops\Model\Core;
use Cops\Exception\Archive\AdapterException;

/**
 * Common controller model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Controller
{
    /**
     * Model getter for controllers
     *
     * @param string $model
     * @param array  $args
     *
     * @return \Cops\Model\Common
     */
    protected function getModel($model, $args=array())
    {
        $app = Core::getApp();
        return $app['core']->getModel($model, $args);
    }
}
