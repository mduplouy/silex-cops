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
 * Common controller model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Controller
{
    /**
     * Model getter for controllers
     *
     * @param string $model
     *
     * @return \Cops\Model\Common
     */
    protected function getModel($model)
    {
        $app = \Cops\Model\Core::getApp();
        return $app['core']->getModel($model);
    }
}
