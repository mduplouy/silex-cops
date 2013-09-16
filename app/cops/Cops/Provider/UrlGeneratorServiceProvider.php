<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Cops\Model\Routing\UrlGenerator;

/**
 * UrlGeneratorServiceProvider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UrlGeneratorServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['url_generator'] = $app->share(function ($app) {
            $app->flush();
            return new UrlGenerator($app['routes'], $app['request_context']);
        });
    }

    public function boot(Application $app)
    {
    }
}