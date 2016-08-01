<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Command\Provider;

use Silex\Application;
use LExpress\Silex\ConsoleServiceProvider;

class CommandServiceProvider extends ConsoleServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register(Application $app)
    {
        return parent::register($app);
    }

    /**
     * @inheritDoc
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        // Register console
        $app['console.name']    = 'SilexCops';
        $app['console.version'] = '1.0';

        $app['command.cache-warmup'] = $app->share(function ($app) {
            return new \Cops\Command\GenerateThumbnails('generate:thumbnails', $app);
        });
        $app['command.init-database'] = $app->share(function ($app) {
            return new \Cops\Command\InitDatabase('generate:thumbnails', $app);
        });
        $app['command.algolia-index'] = $app->share(function ($app) {
            return new \Cops\Command\AlgoliaIndexer('algolia:reindex', $app);
        });
    }
}
