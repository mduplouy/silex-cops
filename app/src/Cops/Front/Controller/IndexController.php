<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Front\Controller;

use Silex\ControllerProviderInterface;
use Silex\Application;

/**
 * Index controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class IndexController implements ControllerProviderInterface
{
    /**
     * @inheritDoc
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match('/', __CLASS__.'::indexAction')
            ->value('database', 'default')
            ->bind('homepage');

        return $controller;
    }

    /**
     * Index action - Display the homepage
     *
     * @param Application $app
     *
     * @return string
     */
    public function indexAction(Application $app)
    {
        $latestBooks = $app['collection.book']
            ->getLatest($app['config']->getValue('last_added'))
            ->addAuthors($app['collection.author']);

        $countAll = $app['collection.book']->countAll();

        $series = $app['collection.serie'];

        $tags = $app['collection.tag']
            ->setMaxResults($app['config']->getValue('homepage_tags'))
            ->findAllWithBookCount();

        $subTags = $tags->createSubTagsCollection($app['collection.tag']);

        $authors = $app['collection.author'];

        return $app['twig']->render($app['config']->getTemplatePrefix().'homepage.html.twig', array(
            'pageTitle'          => $app['translator']->trans('Homepage'),
            'latestBooks'        => $latestBooks,
            'displayShowAlllink' => $latestBooks->count() < $countAll,
            'seriesAggregated'   => $series->countGroupedByFirstLetter(),
            'countSeries'        => $series->countAll(),
            'authorsAggregated'  => $authors->countGroupedByFirstLetter(),
            'countAuthors'       => $authors->countAll(),
            'tags'               => $subTags,
            'countTags'          => $subTags->count(),
        ));
    }
}
