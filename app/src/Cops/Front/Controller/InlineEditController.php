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
use Cops\Core\Entity\Book\EditableBook;
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
     * @param Application $app    Application instance
     * @param int         $id
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
                array($book, $value, $app)
            );
        }

        throw new \InvalidArgumentException(sprintf('Field %s cannot be edited', $field));
    }

    /**
     * Update book author & author sort
     *
     * @param EditableBook $book
     * @param string       $authors
     *
     * @return bool
     */
    protected function updateBookAuthor(EditableBook $book, $authors)
    {
        return (bool) $book->updateAuthor($authors);
    }

    /**
     * Update book title
     *
     * @param EditableBook $book
     * @param string       $title
     *
     * @return bool
     */
    protected function updateBookTitle(EditableBook $book, $title)
    {
        return (bool) $book->updateTitle($title);
    }

    /**
     * Update boook publication date
     *
     * @param EditableBook $book
     * @param string       $pubDate
     * @param Application  $app
     *
     * @return bool
     */
    protected function updateBookPubDate(EditableBook $book, $pubDate, Application $app)
    {
        // Translate format like in view to build DateTime object
        $dateFormat = $app['translator']->trans('m/d/Y');

        $output = false;
        if ($dateTime = \DateTime::createFromFormat($dateFormat, $pubDate)) {
            $output = (bool) $book->updatePubDate($dateTime);
        }

        return $output;
    }

    /**
     * Update book comment
     *
     * @param EditableBook $book
     * @param string       $comment
     *
     * @return bool
     */
    protected function updateBookComment(EditableBook $book, $comment)
    {
        return (bool) $book->updateComment($comment);
    }

    /**
     * Update book tags
     *
     * @param  EditableBook $book
     * @param  array        $tagNames
     * @param  Application  $app
     *      *
     * @return array
     */
    protected function updateBookTags(EditableBook $book, array $tagNames, Application $app)
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
