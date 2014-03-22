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

/**
 * Inline edit controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class InlineEditController implements ControllerProviderInterface
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

        $controller->post('/{id}', __CLASS__.'::editAction')
            ->assert('id', '\d+')
            ->convert('id', function ($id) { return (int) $id; })
            ->bind('inline_edit_book');

        return $controller;
    }

    public function editAction(Application $app, $id)
    {
        $field = $app['request']->get('name');
        $value = $app['request']->get('value');

        switch ($field) {

            case 'pubdate':
                $output = $this->updateBookPublicationDate($app, $id, $value);
                break;
            case 'title':
                $output = $this->updateBookTitle($app, $id, $value);
                break;
            case 'author':
                $output = $this->updateBookAuthor($app, $id, $value);
                break;
        }

        return (bool) $output;
    }

    /**
     * Update book author & author sort
     *
     * @param Application $app
     * @param int         $bookId
     * @param string      $authors
     *
     * @return bool
     */
    protected function updateBookAuthor(Application $app, $bookId, $authors)
    {
        return $app['model.book']->updateAuthor($authors, $bookId);
    }

    /**
     * Update book title
     *
     * @param Application $app
     * @param int         $bookId
     * @param string      $title
     *
     * @return bool
     */
    protected function updateBookTitle(Application $app, $bookId, $title)
    {
        return $app['model.book']->updateTitle($title, $bookId);
    }

    /**
     * Update boook publication date
     *
     * @param Application $app
     * @param int         $bookId
     * @param string      $pubDate
     *
     * @return bool
     */
    protected function updateBookPublicationDate(Application $app, $bookId, $pubDate)
    {
        // Translate format like in view to build DateTime object
        $dateFormat = $app['translator']->trans("m/d/Y");

        if ($dateTime = \DateTime::createFromFormat($dateFormat, $pubDate)) {
            return $app['model.book']->updatePublicationDate($dateTime, $bookId);
        }
        return false;
    }
}