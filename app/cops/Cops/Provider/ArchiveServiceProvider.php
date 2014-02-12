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
use Cops\Model\Archive\ArchiveFactory;

/**
 * Archive service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ArchiveServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $archive = new ArchiveFactory();

        $app['archive'] = $app->share(function ($app, $archive) {
            return $archive->getInstance()
        });
    }

    public function boot(Application $app)
    {
    }
}
