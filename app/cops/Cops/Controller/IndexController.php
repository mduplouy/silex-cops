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
     *
     * @param \Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match("/", get_called_class().'::indexAction')
            ->bind('homepage');
        return $controller;
    }

    /**
     * Index action
     * Display the homepage
     *
     * @param \Silex\Application $app
     *
     * @return string
     */
    public function indexAction(\Silex\Application $app)
    {
        $serieList = $this->getModel('Serie')->getAggregatedList();
        $countSeries = 0;
        foreach($serieList as $nbSerie) {
            $countSeries += $nbSerie;
        }

        $authorList = $this->getModel('Author')->getAggregatedList();
        $countAuthors = 0;
        foreach($authorList as $nbAuthor) {
            $countAuthors += $nbAuthor;
        }

        return $app['twig']->render($app['config']->getTemplatePrefix().'homepage.html', array(
            'pageTitle' => $app['translator']->trans('Homepage'),
            'latestBooks' => $this->getModel('Book')->getLatest(),
            'seriesAggregated' => $serieList,
            'countSeries' => $countSeries,
            'authorsAggregated' => $authorList,
            'countAuthors' => $countAuthors,
        ));
    }
}
