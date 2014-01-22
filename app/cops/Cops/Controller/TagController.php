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

use Cops\Model\Controller;
use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Cops\Exception\TagException;
use Cops\Exception\Archive\AdapterException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Tag controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class TagController extends Controller implements ControllerProviderInterface
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
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/{id}/{page}', __CLASS__.'::detailAction')
            ->assert('id', '\d+')
            ->assert('page', '\d+')
            ->value('page', 1)
            ->bind('tag_book_list');

        $controller->get('/{id}/download/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('tag_download');

        return $controller;
    }

    /**
     * List books attached to tag
     *
     * @param Application $app   Application instance
     * @param int         $id    TagId
     * @param int         $page  Start offset
     *
     * @return string
     */
    public function detailAction(Application $app, $id, $page)
    {
        try {
            $itemPerPage = $app['config']->getValue('tag_page_size');
            $tag = $this->getModel('Tag')->load($id);
            $books = $tag->getAllBooks($itemPerPage * ($page-1), $itemPerPage);
            $totalBooks = $books->getResource()->getTotalRows();
        } catch (TagException $e) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        return $app['twig']->render($app['config']->getTemplatePrefix().'tag.html', array(
            'tag'        => $tag,
            'books'      => $books,
            'totalBooks' => $totalBooks,
            'pageTitle'  => $tag->getName(),
            'pageNum'    => $page,
            'totalRows'  => $totalBooks,
            'pageCount'  => ceil($totalBooks / $itemPerPage),
        ));
    }

    /**
     * Download all books as archive file
     *
     * @param Application $app
     * @param int         $id     The Tag ID
     * @param string      $format The archive file format (zip|tar.gz)
     *
     * @return string
     */
    public function downloadAction(Application $app, $id, $format)
    {
        try {
            $tag = $this->getModel('Tag')->load($id);
        } catch (TagException $e) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        try {
            $archiveClass = $this->getModel('Archive\\ArchiveFactory', $format)
                ->getInstance();
        } catch (AdapterException $e) {
            return $app->redirect(
                $app['url_generator']->generate(
                    'tag_book_list',
                    array(
                        'id' => $tag->getId()
                    )
                )
            );
        }

        try {
            $tagBooks = $this->getModel('BookFile')
                ->getCollection()
                ->getByTagId($tag->getId());

            $archive = $archiveClass->addFiles($tagBooks)
                ->generateArchive();

            return $app
                ->sendFile($archive)
                ->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $tag->getDownloadSafeName().$archiveClass->getExtension()
                );
        } catch (FileNotFoundException $e) {
            return $app->abort(404);
        }
    }

}