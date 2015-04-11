<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Security\Provider;

use Silex\Application;
use Silex\Provider\SecurityServiceProvider as BaseProvider;

/**
 * Security service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SecurityServiceProvider extends BaseProvider
{
    /**
     * @inheritDoc
     */
    public function register(Application $app)
    {
        parent::register($app);

        $app['provider.user'] = $app->share( function ($app) {
            return new \Cops\Security\User\Provider($app['entity.user']);
        });

        // Register security provider
        $app['security.firewalls'] = array(
            'admin' => array(
                'pattern' => '^/admin',
                'http' => true,
                'users' => $app['provider.user'],
            ),
            'default' => array(
                'pattern' => '^.*$',
                'http' => true,
                'users' => $app['provider.user'],
            ),
        );

        $app['security.role_hierarchy'] = array(
            'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_EDIT', 'ROLE_USER_ACTIONS'),
            'ROLE_EDIT'  => array('ROLE_USER', 'ROLE_USER_ACTIONS'),
            'ROLE_USER_ACTIONS' => array('ROLE_USER'),
        );

        $accessRules = array();
        foreach($app['config']->getValue('data_dir') as $urlPrefix => $dataPath) {
            $accessRules[] = array('^/../'.$urlPrefix.'/admin',       'ROLE_ADMIN');
            $accessRules[] = array('^/../'.$urlPrefix.'/inline-edit', 'ROLE_EDIT');
            $accessRules[] = array('^/../'.$urlPrefix.'/user-books',  'ROLE_USER_ACTIONS');
        }
        $app['security.access_rules'] = $accessRules;

        return $app;
    }

    /**
     * @inheritDoc
     */
    public function boot(Application $app)
    {
        parent::boot($app);
    }
}
