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

use Silex\Application as BaseApplication;

/**
 * Overrides mount to dynamicall add locale parameter
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Application extends BaseApplication
{
    /**
     * Mounts controllers under the given route prefix.
     *
     * @param string                                           $prefix      The route prefix
     * @param ControllerCollection|ControllerProviderInterface $controllers A ControllerCollection or a ControllerProviderInterface instance
     *
     * @return Application
     */
    public function mount($prefix, $controllers)
    {
        return parent::mount(
            '/{_locale}'.$prefix,
            $controllers
        );
    }
}
