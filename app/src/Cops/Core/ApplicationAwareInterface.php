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

use Cops\Core\Application as BaseApplication;

/**
 * Application aware interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface ApplicationAwareInterface
{
    /**
     * Set application
     * @param BaseApplication
     */
    public function setApp(BaseApplication $app);
}
