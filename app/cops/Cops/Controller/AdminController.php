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


use Cops\Model\Controller;
use Silex\ControllerProviderInterface;
use Silex\Application;

/**
 * Admin related controller
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AdminController extends Controller implements ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see ControllerProviderInterface::connect()
     *
     * @param Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', __CLASS__.'::indexAction')
            ->bind('admin_index');

        return $controller;
    }

    /**
     * Admin home
     *
     * @param  Application $app Application instance
     *
     * @return string
     */
    public function indexAction(Application $app)
    {
        return $app['twig']->render('admin/home.html', array(
            'pageTitle'     => $app['translator']->trans('Administration'),
        ));
    }

}
