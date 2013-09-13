<?php

namespace Cops\Provider;

use Silex\Application;
use Cops\Model\Routing\UrlGenerator;

class UrlGeneratorServiceProvider extends \Silex\Provider\UrlGeneratorServiceProvider
{

    public function register(Application $app)
    {
        $app['url_generator'] = $app->share(function ($app) {
            $app->flush();
            return new UrlGenerator($app['routes'], $app['request_context']);
        });
    }
}