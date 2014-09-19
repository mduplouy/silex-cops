<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Controller\Admin;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * User Admin related controller
 */
class UserController implements ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see    ControllerProviderInterface::connect()
     *
     * @param  Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', __CLASS__.'::listAction')
            ->bind('admin_user_index');

        $controller->match('/add_user', __CLASS__.'::addAction')
            ->method('GET|POST')
            ->bind('admin_user_add');

        return $controller;
    }

    /**
     * Show user list action
     *
     * @param  Application $app
     *
     * @return string
     */
    public function listAction(Application $app)
    {
        return $app['twig']->render('admin/user/list.html', array(
            'users' => $app['model.user']->getCollection()->getAll()
        ));
    }

    /**
     * Add user action
     *
     * @param  Application $app
     *
     * @return string
     */
    public function addAction(Application $app)
    {
        $user = $app['model.user'];

        $form = $app['form.factory']->create($app['form.type.user'], $user);

        $form->handleRequest($app['request']);

        if ($form->isValid()) {
            $user->setData($form->getData())->save();
        }

        return $app['twig']->render(
            'admin/user/add.html',
            array(
                'user' => $user,
                'form' => $form->createView(),
            )
        );
    }
}