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
     * Latest books collection
     * @var \Cops\Model\Book\Collection
     */
    private $latestBooks;

    /**
     * Author aggregated list
     * @var array
     */
    private $authors;

    /**
     * Author count
     * @var int
     */
    private $nbAuthors;

    /**
     * Serie aggregated list
     * @var array
     */
    private $series;

    /**
     * Serie count
     * @var int
     */
    private $nbSeries;

    /**
     * Tag list
     * @var \Cops\Model\Tag\Collection
     */
    private $tags;

    /**
     * Tag count
     * @var int
     */
    private $nbTags;

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
        $latestBooks = $this->getModel('Book')
            ->getCollection()
            ->getLatest($app['config']->getValue('last_added'));

        $this->listSeries();
        $this->listAuthors();
        $this->listTags($app);

        return $app['twig']->render($app['config']->getTemplatePrefix().'homepage.html', array(
            'pageTitle'         => $app['translator']->trans('Homepage'),
            'latestBooks'       => $latestBooks,
            'seriesAggregated'  => $this->series,
            'countSeries'       => $this->nbSeries,
            'authorsAggregated' => $this->authors,
            'countAuthors'      => $this->nbAuthors,
            'tags'              => $this->tags,
            'countTags'         => $this->nbTags,
        ));
    }

    /**
     * Get serie list and count total
     *
     * @return void
     */
    private function listSeries()
    {
        $serie = $this->getModel('Serie');
        $this->series = $serie->getAggregatedList();
        $this->nbSeries = $serie->getResource()->count();
    }

    /**
     * Get authors and count total
     *
     * @return void
     */
    private function listAuthors()
    {
        $author = $this->getModel('Author');
        $this->authors = $author->getAggregatedList();
        $this->nbAuthors = $author->getResource()->count();
    }

    /**
     * Get tags and count total
     *
     * @param  Application $app
     *
     * @return void
     */
    private function listTags($app)
    {
        $this->tags = $this->getModel('Tag')
            ->getCollection()
            ->setFirstResult(0)
            ->setMaxResults($app['config']->getValue('homepage_tags'))
            ->getAll();
        $this->nbTags = $this->tags->getResource()->getTotalRows();
    }
}
