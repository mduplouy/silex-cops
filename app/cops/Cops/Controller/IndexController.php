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
class IndexController implements \Silex\ControllerProviderInterface
{
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
        $controller->match("/", __CLASS__.'::indexAction')
            ->value('database', 'default')
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
        $latestBooks = $app['model.book']
            ->getCollection()
            ->getLatest($app['config']->getValue('last_added'));

        $this->listSeries($app);
        $this->listAuthors($app);
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
     * @param  \Silex\Application $app
     *
     * @return void
     */
    private function listSeries(Application $app)
    {
        $serie = $app['model.serie'];
        $this->series = $serie->getAggregatedList();
        $this->nbSeries = $serie->getResource()->count();
    }

    /**
     * Get authors and count total
     *
     * @param  \Silex\Application $app
     *
     * @return void
     */
    private function listAuthors(Application $app)
    {
        $author = $app['model.author'];
        $this->authors = $author->getAggregatedList();
        $this->nbAuthors = $author->getResource()->count();
    }

    /**
     * Get tags and count total
     *
     * @param  \Silex\Application $app
     *
     * @return void
     */
    private function listTags(Application $app)
    {
        $this->tags = $app['model.tag']
            ->getCollection()
            ->setFirstResult(0)
            ->setMaxResults($app['config']->getValue('homepage_tags'))
            ->getAllWithBookCount();
        $this->nbTags = $this->tags->getResource()->getTotalRows();
    }
}
