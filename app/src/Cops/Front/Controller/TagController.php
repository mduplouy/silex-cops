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
use Cops\Core\Entity\Tag;
use Cops\Core\Entity\Exception\TagNotFoundException;
use Cops\Core\AdapterNotFoundException;

/**
 * Tag controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class TagController implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
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
        if (null !== $tag = $this->loadTagOrRedirect($app, $id)) {

            $itemsPerPage = $app['config']->getValue('tag_page_size');

            $books = $app['collection.book']
                ->setFirstResult(($page - 1) * $itemsPerPage)
                ->setMaxResults($itemsPerPage)
                ->findByTagId($id)
                ->addAuthors($app['collection.author'])
                ->addBookFiles($app['collection.bookfile']);

            $totalBooks = $books->getRepository()->getTotalRows();

            $app['response'] = $app['twig']->render(
                $app['config']->getTemplatePrefix().'tag.html.twig',
                array(
                    'tag'        => $tag,
                    'books'      => $books,
                    'pageTitle'  => $tag->getName(),
                    'pageNum'    => $page,
                    'totalRows'  => $totalBooks,
                    'pageCount'  => ceil($totalBooks / $app['config']->getValue('tag_page_size')),
                )
            );
        }

        return $app['response'];
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
        if (!$tag = $this->loadTagOrRedirect($app, $id)) {
            return $app['response'];
        }

        if (!$archiveClass = $this->getArchiveOrRedirect($app, $format, $tag)) {
            return $app['response'];
        }

        $bookfiles = $app['collection.bookfile']->findByTagId($id);

        $archive = $archiveClass->addFiles($bookfiles)->generateArchive();

        return $app
            ->sendFile($archive)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $app['string-utils']->removeAccents($tag->getName()).$archiveClass->getExtension()
            );
    }

    /**
     * Load tag or redirect to homepage
     *
     * @param Application $app
     * @param int         $id
     *
     * @return Tag|null
     */
    protected function loadTagOrRedirect(Application $app, $id)
    {
        try {
            return $app['entity.tag']->findById($id);
        } catch (TagNotFoundException $e) {
            $app['response'] = $app->redirect($app['url_generator']->generate('homepage'));
        }
    }

    /**
     * Get archive class or redirect
     *
     * @param Application  $app     Application instance
     * @param string       $format  Archive format
     * @param Tag          $tag     Tag instance
     *
     * @return \Cops\Core\Archive\AdapterInterface
     */
    protected function getArchiveOrRedirect(Application $app, $format, Tag $tag)
    {
        try {
            return $app['factory.archive']->getInstance($format);
        } catch (AdapterNotFoundException $e) {
            $app['response'] = $app->redirect(
                $app['url_generator']->generate('tag_book_list', array('id' => $tag->getId()))
            );
        }
    }
}
