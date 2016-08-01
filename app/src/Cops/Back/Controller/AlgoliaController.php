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
use AlgoliaSearch\AlgoliaException;

/**
 * Algolia search Admin related controller
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AlgoliaController implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', __CLASS__.'::indexAction')
            ->bind('admin_algolia_index');

        $controller->match('/reindex_books', __CLASS__.'::reindexBooksAction')
            ->method('POST')
            ->bind('admin_algolia_reindex_books');

        return $controller;
    }

    /**
     * Algolia index action
     *
     * @param  Application $app
     *
     * @return string
     */
    public function indexAction(Application $app)
    {
        try {

            $indexes = $app['algolia-client']->listIndexes();

            $logs    = $app['algolia-client']->getLogs();
            if (!is_array($logs) || !array_key_exists('logs', $logs)) {
                $logs = array('logs' => array());
            }

            $response = $app['twig']->render('admin/algolia/index.html.twig', array(
                'algoliaIndex' => $indexes['items'][0],
                'logs'         => $logs,
            ));

        } catch (AlgoliaException $e) {
            $response = $app['twig']->render('admin/algolia/api-error.html.twig', array(
                'errorMsg' => $e->getMessage(),
            ));
        }

        return $response;
    }

    /**
     * Reindex books action
     *
     * @param  Application $app
     *
     * @return string
     */
    public function reindexBooksAction(Application $app)
    {
        $books = $app['collection.book'];
        $totalBooks = $books->countAll();

        $page = $app['request']->get('start', 0);
        $pageSize = 300;

        $allBooks = $app['collection.book']
            ->setFirstResult($page * $pageSize)
            ->setMaxResults($pageSize - 1)
            ->findAll()
            ->addAuthors($app['collection.author'])
            ->addTags($app['collection.tag'])
            ->addBookFiles($app['collection.bookfile'])
        ;

        $app['factory.search']->getInstance('algolia')->indexBooks($allBooks);

        return json_encode(array(
            'end' => ($page * $pageSize >= $totalBooks),
        ));
    }
}
