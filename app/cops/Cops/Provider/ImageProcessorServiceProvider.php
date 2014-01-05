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
use Cops\Model\ImageProcessor\ImageProcessorFactory;

/**
 * Image processor service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ImageProcessorServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['image_processor'] = $app->share(function ($app) {
            $imageProcessorFactory = new ImageProcessorFactory(
                $app['config']->getValue('image_processor')
            );
            return $imageProcessorFactory->getInstance($app['core']->getModel('Book'));
        });
    }

    public function boot(Application $app)
    {
    }
}
