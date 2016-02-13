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
use Cops\Core\Application;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Cops\Core\Entity\Book;
use Cops\Core\Entity\Exception\BookNotFoundException;
use Cops\Core\Entity\BookFile\FormatUnavailableException;

/**
 * Book controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookController implements ControllerProviderInterface
{
    /**
     * @inheritDoc
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/{id}', __CLASS__.'::detailAction')
            ->assert('id', '\d+')
            ->bind('book_detail');

        $controller->get('/download/{id}/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('book_download');

        $controller->get('/by-date/{page}', __CLASS__.'::listByDateAction')
            ->assert('page', '\d+')
            ->value('page', 1)
            ->bind('book_by_date');

        return $controller;
    }

    /**
     * Show details of a book
     *
     * @param Application $app Silex app instance
     * @param int         $id  BookId
     *
     * @return string
     */
    public function detailAction(Application $app, $id)
    {
        try {
            $book = $app['entity.book']->findById($id);
        } catch (BookNotFoundException $e) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        return $app['twig']->render(
            $app['config']->getTemplatePrefix().'book.html.twig',
            array(
                'pageTitle'   => $book->getTitle(),
                'book'        => $book,
                'serieBooks'  => $app['collection.book']->findOthersFromSameSerie($book),
                'authorBooks' => $app['collection.book']->findOthersFromSameAuthor($book),
                'tags'        => $app['collection.tag']->findByBookId($book->getId())
            )
        );
    }

    /**
     * Download book file
     *
     * @param Application $app    Silex app instance
     * @param int         $id     The book ID
     * @param string      $format The book file format
     *
     * @return void
     */
    public function downloadAction(Application $app, $id, $format)
    {
        if ($book = $this->loadBookOrRedirect($app, $id)) {

            if ($bookFile = $this->getBookFileOrRedirect($app, $format, $book) ) {

                $app['response'] = $app
                    ->sendFile($bookFile->getFilePath(), 200, array($bookFile->getContentTypeHeader()))
                    ->setContentDisposition(
                        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                        $bookFile->getFileName()
                    );
            }
        }

        return $app['response'];
    }

    /**
     * Show books sorted by add date
     *
     * @param Application $app
     * @param int         $page
     *
     * @return string
     */
    public function listByDateAction(Application $app, $page)
    {
        $itemPerPage = $app['config']->getValue('by_date_page_size');

        $books = $app['collection.book']->setFirstResult(($page-1) * $itemPerPage)
            ->setMaxResults($itemPerPage)
            ->findSortedByDate()
            ->addAuthors($app['collection.author'])
            ->addTags($app['collection.tag'])
            ->addBookFiles($app['collection.bookfile']);

        $totalBooks = $app['collection.book']->countAll();

        return $app['twig']->render($app['config']->getTemplatePrefix().'books_by_date.html.twig', array(
            'books'      => $books,
            'totalBooks' => $totalBooks,
            'pageTitle'  => $app['translator']->trans('All books sorted by add date'),
            'pageNum'    => $page,
            'totalRows'  => $totalBooks,
            'pageCount'  => ceil($totalBooks / $itemPerPage),
        ));
    }

    /**
     * Load book or redirect to homepage
     *
     * @param Application $app
     * @param int         $id
     *
     * @return Book|null
     */
    protected function loadBookOrRedirect(Application $app, $id)
    {
        try {
            return $app['entity.book']->findById($id);
        } catch (BookNotFoundException $e) {
            $app['response'] = $app->redirect($app['url_generator']->generate('homepage'));
        }
    }

    /**
     * Get book file instance or redirect
     *
     * @param Application  $app     Application instance
     * @param string       $format  Bookfile format
     * @param Book         $book    Book instance
     *
     * @return \Cops\Core\BookFile\AdapterInterface|null
     */
    protected function getBookFileOrRedirect(Application $app, $format, Book $book)
    {
        try {
            return $book->getFile($format);
        } catch (FormatUnavailableException $e) {
            $url = $app['url_generator']->generate('book_detail', array('id' => $book->getId()));
            $app['response'] = $app->redirect($url);
        }
    }
}
