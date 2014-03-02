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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Cops\Model\BookFile\BookFileFactory;

/**
 * Book controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SearchController implements \Silex\ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see \Silex\ControllerProviderInterface::connect()
     *
     * @param Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

         $controller->post('/', __CLASS__.'::searchAction')
            ->bind('search');

        $controller->get('/{keywords}/results/{page}', __CLASS__.'::resultAction')
            ->convert('keywords', function($searchTerm) {
                 return filter_var($searchTerm, FILTER_SANITIZE_ENCODED);
             })
            ->assert('keywords', '([\w-]{2,})?([\w]{3,})([\w-]{2,})?')
            ->assert('page', '\d+')
            ->value('page', 1)
            ->bind('search_results');

        return $controller;
    }

    /**
     * Search action
     * Sanitize keywords and redirect to result page
     *
     * @param Request     $request Request
     * @param Application $app     Silex application instance
     *
     */
    public function searchAction(Request $request, Application $app)
    {
        $core = $app['core'];
        $keywords = preg_replace('([^\\w])', '-', $app['utils']->removeAccents($request->get('keywords')));

        return $app->redirect(
            $app['url_generator']->generate('search_results',
                array('keywords' => $keywords)
            )
        );
    }

    /**
     * Result action
     * Shows books matching the keywords
     *
     * @param string      $keywords
     * @param Application Silex application instance
     */
    public function resultAction($keywords, Application $app, $page)
    {
        $itemsPerPage = $app['config']->getValue('search_page_size');

        $collection = $app['search']->getResults(explode('-', $keywords), $itemsPerPage, $page);

        $resultCount = $collection->getResource()->getTotalRows();

        // Only one result, go to the book page
        if ($resultCount == 1) {
            $book = $collection->getIterator()->current();
            $bookUrl = $app['url_generator']->generate('book_detail', array('id' => $book->getId()));
            return $app->redirect($bookUrl, 302);
        }

        $pageCount   = ceil($resultCount / $itemsPerPage);

        return $app['twig']->render($app['config']->getTemplatePrefix().'search_results.html', array(
            'pageTitle'   => $app['translator']->trans('Search results'),
            'books'       => $collection,
            'resultCount' => $resultCount,
            'pageNum'     => $page,
            'pageCount'   => $pageCount,
        ));
    }

}