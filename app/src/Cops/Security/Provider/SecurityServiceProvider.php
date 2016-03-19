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
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Security service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SecurityServiceProvider extends BaseProvider
{
    /**
     * Form auth method
     */
    const AUTH_METHOD_FORM = 'form';

    /**
     * @inheritDoc
     */
    public function register(Application $app)
    {
        parent::register($app);
        $config = $app['config'];

        $app['provider.user'] = $app->share(function ($app) {
            return new \Cops\Security\User\Provider($app['entity.user']);
        });

        $adminPath = '^'.$config->getValue('admin_path');

        $app['security.firewalls'] = $this->defineFirewalls($app, $adminPath);

        $roles = array(
            'ROLE_ADMIN' => array('ROLE_USER'),
        );

        if ($config->getValue('inline_edit_enable')) {
            $roles['ROLE_ADMIN'][] = 'ROLE_EDIT';
            $roles['ROLE_EDIT']  = array('ROLE_USER');
        }

        if ($config->getValue('user_actions_enable')) {
            $roles['ROLE_ADMIN'][] = 'ROLE_USER_ACTIONS';
            if (array_key_exists('ROLE_EDIT', $roles)) {
                $roles['ROLE_EDIT'][] = 'ROLE_USER_ACTIONS';
            }
            $roles['ROLE_USER_ACTIONS']  = array('ROLE_USER');
        }

        $app['security.role_hierarchy'] = $roles;

        $accessRules = array(
            array($adminPath, 'ROLE_ADMIN'),
        );
        foreach($app['config']->getValue('data_dir') as $urlPrefix => $dataPath) {
            $accessRules[] = array('^/'.$urlPrefix.'/../inline-edit', 'ROLE_EDIT');
            $accessRules[] = array('^/'.$urlPrefix.'/../user-books',  'ROLE_USER_ACTIONS');
        }
        $app['security.access_rules'] = $accessRules;

        return $app;
    }

    /**
     * Define the firewalls
     *
     * @param Application $app
     * @param string      $adminPath
     *
     * @return array
     */
    private function defineFirewalls(Application $app, $adminPath)
    {
        $firewalls = array();
        $authMethod = $app['config']->getValue('auth_method');
        $userProvider = $app['provider.user'];

        if ($authMethod == self::AUTH_METHOD_FORM) {
            // Login page
            $firewalls['login'] = array(
                'pattern'   => '^/login$',
                'anonymous' => true,
                'context'   => 'default',
            );

            $app->get('/login', function(\Symfony\Component\HttpFoundation\Request $request) use ($app) {
                $lastError = $app['security.last_error'];
                $app['locale'] = $app['config']->getValue('default_lang');
                return $app['twig']->render('login.html.twig', array(
                    'error'         => $lastError($request),
                    'last_username' => $app['session']->get('_security.last_username'),
                ));
            });
        }

        $firewalls['admin'] = $this->getFirewalParams(
            $adminPath,
            true, // Always enforce auth for administration
            $authMethod,
            $userProvider
        );

        $firewalls['default'] = $this->getFirewalParams(
            '^.*$',
            (bool) $app['config']->getValue('use_auth'),
            $authMethod,
            $userProvider
        );


        return $firewalls;
    }

    /**
     * Get firewall params
     *
     * @param string                $pattern
     * @param bool                  $useAuth
     * @param string                $authMethod
     * @param UserProviderInterface $userProvider
     *
     * @return array
     */
    private function getFirewalParams($pattern, $useAuth, $authMethod, UserProviderInterface $userProvider)
    {
        $http = true;
        $form = false;

        if ($authMethod == self::AUTH_METHOD_FORM) {
            $http = false;
            $form = array(
                'login_path' => '/login',
                'check_path' => '/login_check',
            );
        }

        return array(
            'pattern'   => $pattern,
            'context'   => 'default',
            'anonymous' => !$useAuth,
            'http'      => $http,
            'form'      => $form,
            'logout'    => array('logout_path' => '/logout', 'invalidate_session' => true),
            'users'     => $userProvider,
        );

    }

    /**
     * @inheritDoc
     */
    public function boot(Application $app)
    {
        parent::boot($app);
    }
}
