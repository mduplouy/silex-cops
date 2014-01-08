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
class BookController
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
        $controller->get('/{id}', __CLASS__.'::detailAction')
            ->assert('id' ,'\d+')
            ->bind('book_detail');

        $controller->get('/download/{id}/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('book_download');

        return $controller;
    }

    /**
     * Show details of a book
     *
     * @param \Silex\Application $app Silex app instance
     * @param int                $id  BookId
     *
     * @return string
     */
    public function detailAction(\Silex\Application $app, $id)
    {
        try {
            $book = $this->getModel('Book')->load($id);
        } catch(\Cops\Exception\BookException $e) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        return $app['twig']->render(
            $app['config']->getTemplatePrefix().'book.html',
            array(
                'pageTitle' => $book->getTitle(),
                'book' => $book
            )
        );
    }

    /**
     * Download book file
     *
     * @param \Silex\Application $app    Silex app instance
     * @param int                $id     The book ID
     * @param string             $format The book file format
     *
     * @return void
     */
    public function downloadAction(
        \Silex\Application $app,
        $id,
        $format = BookFileFactory::TYPE_EPUB
    ) {
        try {
            $book = $this->getModel('Book')->load($id);

            $bookFile = $book->getFile(strtoupper($format));

        } catch(\Cops\Exception\BookFile\AdapterException $e) {
            return $app->redirect(
                $app['url_generator']->generate(
                    'book_detail',
                    array(
                        'id' => $book->getId()
                    )
                )
            );
        } catch(\Cops\Exception\BookException $e) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        if ($file = $bookFile->getFilePath()) {
            return $app
                ->sendFile($file)
                ->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $bookFile->getFileName()
                );
        }
        return $app->abort(404);
    }
}
