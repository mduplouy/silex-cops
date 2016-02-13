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
use Cops\Core\Entity\Author;
use Cops\Core\Entity\Exception\AuthorNotFoundException;
use Cops\Core\AdapterNotFoundException;

/**
 * Author controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AuthorController implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/{id}/download/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('author_download');

        $controller->get('/list/{letter}/{page}', __CLASS__.'::listAction')
            ->assert('letter', '\w+|0')
            ->value('page', 1)
            ->bind('author_list');

        $controller->get('/{id}/{page}', __CLASS__.'::detailAction')
            ->assert('id', '\d+')
            ->assert('page', '\d+')
            ->value('page', 1)
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

        $authorBooks = $app['collection.bookfile']->findByAuthorId($author->getId());

        $archive = $archiveClass->addFiles($authorBooks)
            ->generateArchive();

        return $app
            ->sendFile($archive)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $app['string-utils']->removeAccents($author->getName()).$archiveClass->getExtension()
            );
    }

    /**
     * List author action
     *
     * @param Silex\Application $app
     * @param string|int        $letter
     *
     * @return string
     */
    public function listAction(Application $app, $letter=0)
    {
        if ($letter == '0') {
            $letter = '#';
        }

        $authors = $app['collection.author']->findByFirstLetter($letter);

        return $app['twig']->render($app['config']->getTemplatePrefix().'author_list.html.twig', array(
            'letter' => $letter,
            'authors' => $authors,
            'pageTitle' => sprintf($app['translator']->trans('Authors beginning by %s'), $letter),
        ));
    }

    /**
     * Author detail action
     *
     * @param Application $app   Application instance
     * @param id          $id    Author ID
     * @param int         $page  Current page
     */
    public function detailAction(Application $app, $id, $page)
    {
        if (null !== $author = $this->loadAuthorOrRedirect($app, $id)) {

            $itemsPerPage = $app['config']->getValue('author_page_size');

            $books = $app['collection.book']
                ->setFirstResult(($page - 1) * $itemsPerPage)
                ->setMaxResults($itemsPerPage)
                ->findByAuthorId($id);

            $totalBooks = $books->getRepository()->getTotalRows();

            $app['response'] = $app['twig']->render(
                $app['config']->getTemplatePrefix().'author.html.twig',
                 array(
                    'author'     => $author,
                    'books'      => $books,
                    'pageTitle'  => $author->getSort(),
                    'pageNum'    => $page,
                    'totalRows'  => $totalBooks,
                    'pageCount'  => ceil($totalBooks / $app['config']->getValue('author_page_size')),
                )
            );
        }

        return $app['response'];
    }

    /**
     * Load author or redirect to homepage
     *
     * @param Application $app
     * @param int             $id
     *
     * @return Author|null
     */
    protected function loadAuthorOrRedirect(Application $app, $id)
    {
        try {
            return $app['entity.author']->findById($id);
        } catch (AuthorNotFoundException $e) {
            $app['response'] = $app->redirect($app['url_generator']->generate('homepage'));
        }
    }

    /**
     * Get archive class or redirect
     *
     * @param Application  $app     Application instance
     * @param string       $format  Archive format
     * @param Author       $author  Author instance
     *
     * @return \Cops\Core\Archive\AdapterInterface
     */
    protected function getArchiveOrRedirect(Application $app, $format, Author $author)
    {
        try {
            return $app['factory.archive']->getInstance($format);
        } catch (AdapterNotFoundException $e) {
            $url = $app['url_generator']
                ->generate('author_detail', array('id' => $author->getId()));
            $app['response'] = $app->redirect($url);
        }
    }
}
