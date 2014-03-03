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

use Cops\Model\FactoryInterface;
use Silex\Application as BaseApplication;

abstract class FactoryAbstract implements FactoryInterface
{
    /**
     * Application instance
     * @var \Silex\Application
     */
    protected $app;

    /**
     * Constructor
     *
     * @param \Silex\Application $app
     */
    public function __construct(BaseApplication $app)
    {
        $this->app = $app;
    }
}
