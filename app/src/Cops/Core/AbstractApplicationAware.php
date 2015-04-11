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

use Cops\Core\ApplicationAwareInterface;
use Cops\Core\UseServiceKeyInterface;
use Cops\Core\Application as BaseApplication;

/**
 * Abstract implementation of ApplicationAwareInterface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractApplicationAware implements ApplicationAwareInterface, UseServiceKeyInterface
{
    /**
     * Application instance
     * @var Application
     */
    protected $app;

    /**
     * Service key
     * @var string
     */
    protected $serviceKey;

    /**
     * Set application
     *
     * @param  BaseApplication $app
     *
     * @return self
     */
    public function setApp(BaseApplication $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Set service key for collection operations
     *
     * @param string $key
     *
     * @return self
     */
    public function setServiceKey($key)
    {
        $this->serviceKey = $key;
        return $this;
    }

    /**
     * Get service key
     *
     * @return string
     */
    public function getServiceKey()
    {
        return $this->serviceKey;
    }
}
