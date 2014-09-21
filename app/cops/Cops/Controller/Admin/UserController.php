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
use Cops\Model\User;
use Symfony\Component\Form\FormError;

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

        $controller->match('/add', __CLASS__.'::addAction')
            ->method('GET|POST')
            ->bind('admin_user_add');

        $controller->match('/edit/{id}', __CLASS__.'::editAction')
            ->assert('id', '\d+')
            ->method('GET|POST')
            ->bind('admin_user_edit');

        $controller->get('/delete/{id}', __CLASS__.'::deleteAction')
            ->assert('id', '\d+')
            ->bind('admin_user_delete');

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

        $form = $this->handleForm($app, $user);

        return $app['twig']->render(
            'admin/user/add.html',
            array(
                'user' => $user,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Edit user action
     *
     * @param  Application $app
     * @param  int         $id
     *
     * @return string
     */
    public function editAction(Application $app, $id)
    {
        $user = $app['model.user']->load($id);

        $form = $this->handleForm($app, $user);

        return $app['twig']->render(
            'admin/user/edit.html',
            array(
                'user' => $user,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Delete user action
     *
     * @param  Application $app
     * @param  int         $id
     *
     * @return RedirectResponse
     */
    public function deleteAction(Application $app, $id)
    {
        $app['model.user']
            ->load($id)
            ->delete();

        return $app->redirect($app['url_generator']->generate('admin_user_index'));
    }

    /**
     * Handle form & save user
     *
     * @param  Application $app
     * @param  User        $user
     *
     * @return Form
     */
    private function handleForm(Application $app, User $user)
    {
        /**
         * @var \Symfony\Component\Form\Form
         */
        $form = $app['form.factory']->create($app['form.type.user'], $user);
        $form->handleRequest($app['request']);

        if ($form->isValid()) {
            try {
                $user->save();
                $app->redirect($app['url_generator']->generate('admin_user_index'))->send();
            } catch (\PDOException $e) {

                if ($e->getCode() == 23000) {
                    $error = new FormError($app['translator']->trans('Username already in use', array(), 'validators'));
                    $form->get('username')->addError($error);
                } else {
                    throw $e;
                }
            }
        }

        return $form;
    }
}