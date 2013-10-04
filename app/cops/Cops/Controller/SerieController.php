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
use Cops\Model\BookFile\BookFileFactory;

/**
 * Serie controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SerieController
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
            ->bind('serie_download');

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
        $serie = $this->getModel('Serie')->load($id);

        $serieBooks = $this->getModel('BookFile')->getCollectionBySerieId($serie->getId());

        $archiveClass = $this->getModel('Archive\\ArchiveFactory', $format)
            ->getInstance();

        $archive = $archiveClass->addFiles($serieBooks)
            ->generateArchive();

        $archiveClass->sendHeaders($serie->getName(), filesize($archive));
        readfile($archive);

        exit;
    }

}
