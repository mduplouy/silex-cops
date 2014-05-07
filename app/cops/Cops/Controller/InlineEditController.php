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
use Cops\Exception\BookException;

/**
 * Inline edit controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class InlineEditController implements ControllerProviderInterface
{
    /**
     * Book field / controller method mapping
     * @var array
     */
    private $fieldMethodMap = array(
        'comment' => 'updateBookComment',
        'pubdate' => 'updateBookPublicationDate',
        'title'   => 'updateBookTitle',
        'author'  => 'updateBookAuthor',
        'tags'    => 'updateBookTags',
    );

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

    /**
     * Main edit action, execute specific update action depending on sent value
     *
     * @param Application $app Application instance
     * @param int         $id  Book id
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function editAction(Application $app, $id)
    {
        try {
            $app['model.book']->load($id);
        } catch (BookException $e) {
            return false;
        }

        $field = $app['request']->get('name');
        $value = $app['request']->get('value');

        if (array_key_exists($field, $this->fieldMethodMap)) {
            return call_user_func_array(
                array($this, $this->fieldMethodMap[$field]),
                array($app, $id, $value)
            );
        }

        throw new \InvalidArgumentException(sprintf('Field %s cannot be edited', $field));
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
        return (bool) $app['model.book']->updateTitle($title, $bookId);
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
        $dateTime = \DateTime::createFromFormat($dateFormat, $pubDate);
        return (bool) $app['model.book']->updatePublicationDate($dateTime, $bookId);
    }

    /**
     * Update book comment
     *
     * @param Application $app
     * @param int         $bookId
     * @param string      $comment
     *
     * @return bool
     */
    protected function updateBookComment(Application $app, $bookId, $comment)
    {
        return (bool) $app['model.book']->updateComment($comment, $bookId);
    }

    /**
     * Update book tags
     *
     * @param  Application $app
     * @param  int         $bookId
     * @param  array       $tagNames
     *
     * @return array
     */
    protected function updateBookTags(Application $app, $bookId, array $tagNames)
    {
        /**
         * @var \Cops\Model\Tag
         */
        $tag = $app['model.tag'];

        // Remove existing associated tags
        $tag->deleteFromBook($bookId);

        $output = array();
        // Associate each to to book
        foreach ($tagNames as $tagName) {

            $newTag = clone($tag);
            $newTag->setName($tagName)
                ->loadByName($tagName)
                ->associateToBook($bookId, $tagName);

            $output[] = array(
                'id'   => $newTag->getId(),
                'name' => $newTag->getName(),
                'url'  => $app['url_generator']->generate(
                    'tag_book_list',
                    array('id' => $newTag->getId())
                )
            );
        }

        return json_encode($output);
    }
}