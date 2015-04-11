<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Back\Controller;

use Silex\ControllerProviderInterface;
use Cops\Core\Application;

/**
 * Admin related controller
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class IndexController implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
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
        return $app['twig']->render('admin/home.html.twig', array(
            'pageTitle' => $app['translator']->trans('Administration'),
        ));
    }

}
