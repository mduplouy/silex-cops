<?php

namespace Cops\Provider;

use Silex\Application;

class UrlGeneratorServiceProvider extends \Silex\Provider\UrlGeneratorServiceProvider
{

    public function register(Application $app)
    {
        $app['url_generator'] = $app->share(function ($app) {
            $app->flush();

            return new \Cops\Model\Routing\UrlGenerator($app['routes'], $app['request_context']);
        });
    }
}