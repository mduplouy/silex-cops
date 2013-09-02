<?php
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
        ));

        $sql = "SELECT * FROM authors";
        $posts = $app['db']->fetchAll($sql);
        var_dump($posts);

        $sql = "SELECT name FROM sqlite_master
            WHERE type='table'
            ORDER BY name;";

        $posts = $app['db']->fetchAll($sql);

    }

}