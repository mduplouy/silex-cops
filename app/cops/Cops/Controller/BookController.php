<?php

namespace Cops\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Book controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookController
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
        $controller->get('/{id}', __CLASS__.'::detailAction')
            ->assert('id' ,'\d+');

        $controller->get('/list/{page}', __CLASS__.'::listAction')
            ->assert('page', '\d+')
            ->value('page', 1);

        return $controller;
    }

    /**
     * Show details of a book
     *
     * @param int $id BookId
     */
    public function detailAction($id)
    {
        return __FUNCTION__.$id;
    }

    public function listAction($page)
    {
        return __FUNCTION__.$page;
    }
}