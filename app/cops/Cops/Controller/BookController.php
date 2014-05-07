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


use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Cops\Model\BookFile\BookFileFactory;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Cops\Exception\BookException;
use Cops\Exception\BookFile\FormatUnavailableException;

/**
 * Book controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookController implements ControllerProviderInterface
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
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/{id}', __CLASS__.'::detailAction')
            ->assert('id', '\d+')
            ->bind('book_detail');

        $controller->get('/download/{id}/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('book_download');

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
    public function detailAction(\Silex\Application $app, $id)
    {
        try {
            $book = $app['model.book']->load($id);
        } catch (BookException $e) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        return $app['twig']->render(
            $app['config']->getTemplatePrefix().'book.html',
            array(
                'pageTitle' => $book->getTitle(),
                'book' => $book,
                'allTags' => $app['model.tag']->getCollection()->getAllNames()
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
    public function downloadAction(Application $app, $id, $format = BookFileFactory::TYPE_EPUB)
    {
        try {
            $book = $app['model.book']->load($id);

            $bookFile = $book->getFile(strtoupper($format));

            return $app
                ->sendFile($bookFile->getFilePath(), 200, array($bookFile->getContentTypeHeader()))
                ->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $bookFile->getFileName()
                );
        } catch (BookException $e) {
            $redirect = $app->redirect($app['url_generator']->generate('homepage'));
        } catch (FormatUnavailableException $e) {
            $redirect = $app->redirect(
                $app['url_generator']->generate('book_detail', array('id' => $book->getId()))
            );
        } catch (FileNotFoundException $e) {
            $redirect = $app->abort(404);
        }
        return $redirect;
    }
}
