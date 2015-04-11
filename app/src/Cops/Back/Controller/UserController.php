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
use Cops\Core\Entity\User;
use Symfony\Component\Form\FormError;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * User Admin related controller
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserController implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
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
        return $app['twig']->render('admin/user/list.html.twig', array(
            'users' => $app['collection.user']->findAll()
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
        $user = $app['entity.user'];

        $form = $this->handleForm($app, $user);

        if ($form instanceOf \Symfony\Component\Form\Form) {
            $app['response'] = $app['twig']->render(
                'admin/user/add.html.twig',
                array(
                    'user' => $user,
                    'form' => $form->createView(),
                )
            );
        }

        return $app['response'];
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
        $user = $app['entity.user']->findById($id);

        $form = $this->handleForm($app, $user);

        if ($form instanceof \Symfony\Component\Form\Form) {
            $app['response'] = $app['twig']->render(
                'admin/user/edit.html.twig',
                array(
                    'user' => $user,
                    'form' => $form->createView(),
                )
            );
        }

        return $app['response'];
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
        $app['entity.user']->findById($id)->delete();

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
                $app['response'] = $app->redirect($app['url_generator']->generate('admin_user_index'));
            } catch (UniqueConstraintViolationException $e) {
                $error = new FormError($app['translator']->trans('Username already in use', array(), 'validators'));
                $form->get('username')->addError($error);
            }
        }

        return $form;
    }
}
