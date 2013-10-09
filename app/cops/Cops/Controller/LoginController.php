<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Login controller
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class LoginController
    extends \Cops\Model\Controller
    implements \Silex\ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see \Silex\ControllerProviderInterface::connect()
     *
     * @param \Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', __CLASS__.'::indexAction')
            ->bind('login_index');

        $controller->post('/check', __CLASS__.'::indexAction')
            ->bind('login_check');

        return $controller;
    }

    /**
     * Login default action, show the login form
     */
    public function indexAction(\Silex\Application $app, Request $request)
    {

        return $app['twig']->render('form/login.html', array(
            'pageTitle'     => $app['translator']->trans('Please log in'),
        ));
    }
}
