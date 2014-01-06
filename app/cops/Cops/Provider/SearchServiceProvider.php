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
use Cops\Model\Search\SearchFactory;

/**
 * Search service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SearchServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['search'] = $app->share(function ($app) {
            $searchFactory = new SearchFactory(
                $app['config']->getValue('search_engine')
            );
            return $searchFactory->getInstance($app['core']->getModel('Book'));
        });
    }

    public function boot(Application $app)
    {
    }
}
