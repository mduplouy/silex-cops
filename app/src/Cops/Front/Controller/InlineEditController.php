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
use Cops\Core\Entity\Book;
use Cops\Core\Entity\Exception\BookNotFoundException;

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
        'pubdate' => 'updateBookPubDate',
        'title'   => 'updateBookTitle',
        'author'  => 'updateBookAuthor',
        'tags'    => 'updateBookTags',
    );

    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
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
     * @param Application $app   Application instance
     * @param Book        $book  Book id
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function editAction(Application $app, $id)
    {
        try {
            $book = $app['entity.book']->findById($id);
        } catch (BookNotFoundException $e) {
            return false;
        }

        $field = $app['request']->get('name');
        $value = $app['request']->get('value');

        if (array_key_exists($field, $this->fieldMethodMap)) {
            return call_user_func_array(
                array($this, $this->fieldMethodMap[$field]),
                array($app, $book, $value)
            );
        }

        throw new \InvalidArgumentException(sprintf('Field %s cannot be edited', $field));
    }

    /**
     * Update book author & author sort
     *
     * @param Application $app
     * @param Book        $book
     * @param string      $authors
     *
     * @return bool
     */
    protected function updateBookAuthor(Application $app, Book $book, $authors)
    {
        return (bool) $book->updateAuthor($authors);
    }

    /**
     * Update book title
     *
     * @param Application $app
     * @param Book        $book
     * @param string      $title
     *
     * @return bool
     */
    protected function updateBookTitle(Application $app, Book $book, $title)
    {
        return (bool) $book->updateTitle($title);
    }

    /**
     * Update boook publication date
     *
     * @param Application $app
     * @param Book        $book
     * @param string      $pubDate
     *
     * @return bool
     */
    protected function updateBookPubDate(Application $app, Book $book, $pubDate)
    {
        // Translate format like in view to build DateTime object
        $dateFormat = $app['translator']->trans("m/d/Y");
        $dateTime = \DateTime::createFromFormat($dateFormat, $pubDate);

        return (bool) $book->updatePubDate($dateTime);
    }

    /**
     * Update book comment
     *
     * @param Application $app
     * @param Book        $book
     * @param string      $comment
     *
     * @return bool
     */
    protected function updateBookComment(Application $app, Book $book, $comment)
    {
        return (bool) $book->updateComment($comment);
    }

    /**
     * Update book tags
     *
     * @param  Application $app
     * @param  Book        $book
     * @param  array       $tagNames
     *
     * @return array
     */
    protected function updateBookTags(Application $app, Book $book, array $tagNames)
    {
        /**
         * @var \Cops\Core\Entity\Tag
         */
        $tag = $app['entity.tag'];

        // Remove existing associated tags
        $tag->deleteFromBook($book);

        $output = array();
        // Associate each to to book
        foreach ($tagNames as $tagName) {

            $newTag = $app['entity.tag']->setName($tagName);
            $newTag->associateToBook($book);

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
