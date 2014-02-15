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

use Cops\Model\Controller as BaseController;
use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Cops\Model\BookFile\BookFileFactory;

use Cops\Exception\AuthorException;
use Cops\Exception\Archive\AdapterException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Author controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AuthorController extends BaseController implements ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see ControllerProviderInterface::connect()
     *
     * @param Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/{id}/download/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('author_download');

        $controller->get('/list/{letter}/{page}', __CLASS__.'::listAction')
            ->assert('letter', '\w+|0')
            ->value('page', 1)
            ->bind('author_list');

        $controller->get('/{id}', __CLASS__.'::detailAction')
            ->assert('id', '\d+')
            ->bind('author_detail');

        return $controller;
    }

    /**
     * Download all serie books as archive file
     *
     * @param Application $app Application instance
     * @param int         $id     The serie ID
     * @param string      $format The archive file format (zip|tar.gz)
     *
     * @return void
     */
    public function downloadAction(Application $app, $id, $format)
    {
        try {
            $author = $this->getModel('Author')->load($id);
        } catch (AuthorException $e) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        try {
            $archiveClass = $this->getModel('Archive\\ArchiveFactory', $format)
                ->getInstance();
        } catch (AdapterException $e) {
            return $app->redirect(
                $app['url_generator']->generate(
                    'author_detail',
                    array(
                        'id' => $author->getId()
                    )
                )
            );
        }

        try {
            $authorBooks = $this->getModel('BookFile')
                ->getCollection()
                ->getByAuthorId($author->getId());


            $archive = $archiveClass->addFiles($authorBooks)
                ->generateArchive();

            return $app
                ->sendFile($archive)
                ->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $author->getDownloadSafeName().$archiveClass->getExtension()
                );
        } catch (FileNotFoundException $e) {
            return $app->abort(404);
        }
    }

    /**
     * List author action
     *
     * @param Application $app
     * @param string|0    $letter
     *
     * @return string
     */
    public function listAction(Application $app, $letter=0)
    {
        if ($letter === '0') {
            $letter = '#';
        }
        $authors = $this->getModel('Author')->getCollection()->getByFirstLetter($letter);

        return $app['twig']->render($app['config']->getTemplatePrefix().'author_list.html', array(
            'letter' => $letter,
            'authors' => $authors,
            'pageTitle' => sprintf($app['translator']->trans('Authors beginning by %s'), $letter),
        ));
    }

    /**
     * Author detail action
     *
     * @param Application $app Application instance
     * @param id          $id  Author ID
     */
    public function detailAction(Application $app, $id)
    {
        try {
            $author = $this->getModel('Author')->load($id);
        } catch (AuthorException $e) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        return $app['twig']->render($app['config']->getTemplatePrefix().'author.html', array(
            'author'     => $author,
            'pageTitle' => $author->getSort(),
        ));
    }

}