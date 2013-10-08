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

/**
 * Author controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AuthorController
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

        $controller->get('/download/{id}/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('author_download');

        $controller->get('/list/{letter}/{page}', __CLASS__.'::listAction')
            ->assert('letter', '\w+|0')
            ->value('page', 1)
            ->bind('author_list');


        return $controller;
    }

    /**
     * Download all serie books as archive file
     *
     * @param int    $id     The serie ID
     * @param string $format The archive file format (zip|tar.gz)
     *
     * @return void
     */
    public function downloadAction($id, $format)
    {
        $author = $this->getModel('Author')->load($id);

        $authorBooks = $this->getModel('BookFile')->getCollectionByAuthorId($author->getId());

        $archiveClass = $this->getModel('Archive\\ArchiveFactory', array($format))
            ->getInstance();

        $archive = $archiveClass->addFiles($authorBooks)
            ->generateArchive();

        $archiveClass->sendHeaders($author->getName(), filesize($archive));
        readfile($archive);
    }

    /**
     * List series action
     *
     * @param Silex\Application $app
     * @param string|0          $letter
     *
     * @return string
     */
    public function listAction(\Silex\Application $app, $letter=0)
    {
        if ($letter === '0') {
            $letter = '#';
        }
        $authors = $this->getModel('Author')->getCollectionByFirstLetter($letter);

        return $app['twig']->render($app['config']->getTemplatePrefix().'author_list.html', array(
            'letter' => $letter,
            'authors' => $authors,
            'pageTitle' => sprintf($app['translator']->trans('Authors beginning by %s'), $letter),
        ));
    }
}