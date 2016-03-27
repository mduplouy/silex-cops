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

use Cops\Core\Application;
use Cops\Core\Entity\Exception\BookNotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Book controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SearchController implements \Silex\ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->post('/', __CLASS__.'::searchAction')
            ->bind('search');

        $controller->get('/no-result', __CLASS__.'::noResultAction')
            ->bind('search_no_result');

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
     * @param Request     $request
     * @param Application $app
     *
     */
    public function searchAction(Request $request, Application $app)
    {
        $keywords = preg_replace(
            '([^\\w])',
            '-',
            $app['string-utils']->removeAccents($request->get('keywords'))
        );

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
     * @param Application $app
     * @param string      $keywords
     * @param int         $page
     *
     * @return string
     */
    public function resultAction(Application $app, $keywords, $page)
    {
        $itemsPerPage = $app['config']->getValue('search_page_size');

        if (!$books = $this->loadBooksOrRedirect($app, $keywords, $page, $itemsPerPage)) {
            return $app['response'];
        }

        $books->addAuthors($app['collection.author'])
            ->addTags($app['collection.tag'])
            ->addBookFiles($app['collection.bookfile']);

        $totalRows = $books->getRepository()->getTotalRows();

        return $app['twig']->render($app['config']->getTemplatePrefix().'search_results.html.twig', array(
            'pageTitle'   => $app['translator']->trans('Search results'),
            'books'       => $books,
            'totalRows'   => $totalRows,
            'pageNum'     => $page,
            'pageCount'   => ceil($totalRows / $itemsPerPage),
        ));
    }

    /**
     * No result action
     *
     * @param Application $app
     *
     * @return string
     */
    public function noResultAction(Application $app)
    {
        return $app['twig']->render($app['config']->getTemplatePrefix().'search_no_result.html.twig', array(
            'pageTitle'   => $app['translator']->trans('Search results'),
        ));
    }

    /**
     * Load books or redirect to the no result page
     *
     * @param Application $app
     * @param string      $keywords
     * @param int         $page
     * @param int         $itemsPerPage
     *
     * @return \Cops\Core\Entity\BookCollection
     */
    protected function loadBooksOrRedirect(Application $app, $keywords, $page, $itemsPerPage)
    {
        try {
            $books = $app['factory.search']
                ->getInstance($app['config']->getValue('search_engine'))
                ->getResults(explode('-', $keywords), $itemsPerPage, $page);

            // Only one result, go to the book page
            if (1 === $books->getRepository()->getTotalRows()) {
                $book = $books->getIterator()->current();
                $bookUrl = $app['url_generator']->generate('book_detail', array('id' => $book->getId()));
                $app['response'] = $app->redirect($bookUrl, 302);
            } else {
                return $books;
            }

        } catch (BookNotFoundException $e) {
            $app['response'] = $app->redirect($app['url_generator']->generate('search_no_result'));
        }
    }
}
