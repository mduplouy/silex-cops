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

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Index controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class IndexController
    extends \Cops\Model\Controller
    implements \Silex\ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see \Silex\ControllerProviderInterface::connect()
     * @param \Application $app Application instance
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match("/", get_called_class().'::indexAction');
        return $controller;
    }

    /**
     * Index action
     * Display the homepage
     *
     * @param \Silex\Application $app
     * @return string
     */
    public function indexAction(\Silex\Application $app)
    {
        return $app['twig']->render('homepage.html', array(
            'pageTitle' => 'toto',
            'latestBooks' => $this->getModel('Book')->getLatest()
        ));
    }

}