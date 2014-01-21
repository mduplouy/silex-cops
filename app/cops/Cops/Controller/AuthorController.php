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
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Cops\Model\BookFile\BookFileFactory;

use Cops\Exception\AuthorException;

/**
 * Author controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AuthorController extends Controller implements ControllerProviderInterface
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

        $controller->get('/download/{id}/{format}', __CLASS__.'::downloadAction')
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
     * @param int                $id     The serie ID
     * @param string             $format The archive file format (zip|tar.gz)
     *
     * @return void
     */
    public function downloadAction(Application $app, $id, $format)
    {
        $author = $this->getModel('Author')->load($id);

        $authorBooks = $this->getModel('BookFile')->getCollection()->getByAuthorId($author->getId());

        $archiveClass = $this->getModel('Archive\\ArchiveFactory', array($format))
            ->getInstance();

        $archive = $archiveClass->addFiles($authorBooks)
            ->generateArchive();

        return $app
            ->sendFile($archive)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $author->getName().$archiveClass->getExtension()
            );
    }

    /**
     * List author action
     *
     * @param Silex\Application $app
     * @param string|0          $letter
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
     * @param Silex\Application $app Application instance
     * @param id                $id  Author ID
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