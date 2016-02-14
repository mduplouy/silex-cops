<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Core\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Detection\MobileDetect;

/**
 * Mobile Detect service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class MobileDetectServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['mobile_detect'] = $app->share(function () {
            return new MobileDetect();
        });
    }

    public function boot(Application $app)
    {
    }
}
