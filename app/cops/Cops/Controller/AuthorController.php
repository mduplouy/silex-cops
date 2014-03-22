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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Cops\Model\Author;

use Cops\Exception\AuthorException;
use Cops\Exception\Archive\AdapterException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Author controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AuthorController implements ControllerProviderInterface
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
     * Download all author books as archive file
     *
     * @param Application $app Application instance
     * @param int                $id     The serie ID
     * @param string             $format The archive file format (zip|tar.gz)
     *
     * @return void
     */
    public function downloadAction(Application $app, $id, $format)
    {
        if (!$author = $this->loadAuthorOrRedirect($app, $id)) {
            return $app['response'];
        }

        if (!$archiveClass = $this->getArchiveOrRedirect($app, $format, $author)) {
            return $app['response'];
        }

        $authorBooks = $app['model.bookfile']
            ->getCollection()
            ->getByAuthorId($author->getId());

        $archive = $archiveClass->addFiles($authorBooks)
            ->generateArchive();

        // Mark file as "to be deleted"
        $app['delete_file'] = $archive;

        return $app
            ->sendFile($archive)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $author->getDownloadSafeName().$archiveClass->getExtension()
            );
    }

    /**
     * Load author or redirect to homepage
     *
     * @param \Silex\Application $app
     * @param int                $id
     *
     * @return \Cops\Model\Author
     */
    protected function loadAuthorOrRedirect(Application $app, $id)
    {
        try {
            return $app['model.author']->load($id);
        } catch (AuthorException $e) {
            $app['response'] = $app->redirect($app['url_generator']->generate('homepage'));
        }
    }

    /**
     * Get archive class or redirect
     *
     * @param \Silex\Application $app    Application instance
     * @param string             $format Archive format
     * @param \Cops\Model\Author $author Author instance
     *
     * @return \Cops\Model\Archive\ArchiveInterface
     */
    protected function getArchiveOrRedirect(Application $app, $format, Author $author)
    {
        try {
            return $app['factory.archive']->getInstance($format);
        } catch (AdapterException $e) {
            $url = $app['url_generator']->generate(
                'author_detail',
                array(
                    'id' => $author->getId()
                )
            );
            $app['response'] = $app->redirect($url);
        }
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
        $authors = $app['model.author']->getCollection()->getByFirstLetter($letter);

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
        if ($author = $this->loadAuthorOrRedirect($app, $id)) {
            return $app['twig']->render($app['config']->getTemplatePrefix().'author.html', array(
                'author'     => $author,
                'pageTitle' => $author->getSort(),
            ));
        }
        return $app['response'];
    }

}