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
use Cops\Core\Entity\Serie;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Cops\Core\Entity\Exception\SerieNotFoundException;
use Cops\Core\AdapterNotFoundException;

/**
 * Serie controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SerieController implements ControllerProviderInterface
{
    /**
     * @inheritDoc
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/{id}/download/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('serie_download');

        $controller->get('/list/{letter}/{page}', __CLASS__.'::listAction')
            ->assert('letter', '\w+|0')
            ->value('page', 1)
            ->bind('serie_list');

        $controller->get('/{id}', __CLASS__.'::detailAction')
            ->assert('id', '\d+')
            ->bind('serie_detail');

        return $controller;
    }

    /**
     * List series action
     *
     * @param Application $app
     * @param string      $letter
     *
     * @return string
     */
    public function listAction(Application $app, $letter = '0')
    {
        if ($letter == '0') {
            $letter = '#';
        }

        $series = $app['collection.serie']->findByFirstLetter($letter);

        return $app['twig']->render($app['config']->getTemplatePrefix().'serie_list.html.twig', array(
            'letter'    => $letter,
            'series'    => $series,
            'pageTitle' => sprintf($app['translator']->trans('Series beginning by %s'), $letter),
        ));
    }

    /**
     * Serie detail action
     *
     * @param Application  $app
     * @param id           $id
     */
    public function detailAction(Application $app, $id)
    {
        if (null !== $serie = $this->loadSerieOrRedirect($app, $id)) {

            $template = $app['config']->getTemplatePrefix().'serie.html.twig';

            $app['response'] =  $app['twig']->render($template, array(
                'serie'     => $serie,
                'books'     => $app['collection.book']->findBySerieId($id),
                'pageTitle' => $serie->getName(),
            ));
        }

        return $app['response'];
    }

    /**
     * Download all serie books as archive file
     *
     * @param Application  $app
     * @param int          $id     The serie ID
     * @param string       $format The archive file format (zip|tar.gz)
     *
     * @return string
     */
    public function downloadAction(Application $app, $id, $format)
    {
        if (!$serie = $this->loadSerieOrRedirect($app, $id)) {
            return $app['response'];
        }

        if (!$archiveClass = $this->getArchiveOrRedirect($app, $format, $serie)) {
            return $app['response'];
        }

        $serieBooks = $app['collection.bookfile']->findBySerieId($serie->getId());

        $archive = $archiveClass->addFiles($serieBooks)
            ->generateArchive();

        return $app
            ->sendFile($archive)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $app['string-utils']->removeAccents($serie->getName()).$archiveClass->getExtension()
            );
    }

    /**
     * Load serie or redirect to homepage
     *
     * @param Application $app
     * @param int         $id
     *
     * @return Serie|null
     */
    protected function loadSerieOrRedirect(Application $app, $id)
    {
        try {
            return $app['entity.serie']->findById($id);
        } catch (SerieNotFoundException $e) {
            $app['response'] = $app->redirect($app['url_generator']->generate('homepage'));
        }
    }

    /**
     * Get archive class or redirect
     *
     * @param Application  $app     Application instance
     * @param string       $format  Archive format
     * @param Serie        $serie   Serie instance
     *
     * @return \Cops\Core\Archive\AdapterInterface
     */
    protected function getArchiveOrRedirect(Application $app, $format, Serie $serie)
    {
        try {
            return $app['factory.archive']->getInstance($format);
        } catch (AdapterNotFoundException $e) {
            $url = $app['url_generator']
                ->generate('serie_detail', array('id' => $serie->getId()));
            $app['response'] = $app->redirect($url);
        }
    }
}
