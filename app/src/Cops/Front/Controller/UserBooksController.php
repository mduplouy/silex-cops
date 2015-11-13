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
use Cops\Core\Entity\User;
use Cops\Core\Entity\UserBookCollection;
use Cops\Core\Entity\BookFile\BookFileCollection;
use Cops\Core\Archive\AdapterInterface as ArchiveAdapterInterface;

/**
 * User Books controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserBooksController implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->post('/action/{id}', __CLASS__.'::bookAction')
            ->assert('id', '\d+')
            ->bind('user_books_action');

        $actions = $app['entity.user-book']->getAvailableLists();

        $controller->get('/{action}/{page}', __CLASS__.'::listAction')
            ->assert('action', implode('|', array_keys($actions)))
            ->assert('page', '\d+')
            ->value('page', 1)
            ->bind('user_books_list');

        $controller->post('/{action}', __CLASS__.'::selectionAction')
            ->assert('action', implode('|', array_keys($actions)))
            ->bind('user_books_selection_action');

        return $controller;
    }

    /**
     * Book action
     *
     * @param Application $app
     * @param int         $id     Book ID
     * @param string      $action Action to make
     *
     * @return string
     */
    public function bookAction(Application $app, $id)
    {
        /**
         * @var \Cops\Core\Entity\UserBook
         */
        $userBook = $app['entity.user-book'];

        $action = $app['request']->get('action');

        $user = $app['security']->getToken()->getUser();

        $userBook
            ->setUserId($user->getId())
            ->setBookId($id)
            ->setAction($action);

        if ($app['request']->get('status', 'false') == 'false') {
            $return = $userBook->delete();
        } else {
            $return = $userBook->save();
        }

        return $return;
    }

    /**
     * List books related to action
     *
     * @param Application $app
     * @param string      $action
     * @param int         $page
     *
     * @return string
     */
    public function listAction(Application $app, $action, $page)
    {
        $user = $app['security']->getToken()->getUser();

        $userBooks = $app['collection.user-book']
            ->findFromUserIdAndAction($user->getId(), $action);

        $books = $app['collection.book']
            ->setFirstResult(0)
            ->setMaxResults(25)
            ->findById($userBooks->getAllBookIds())
            ->addAuthors($app['collection.author'])
            ->addBookFiles($app['collection.bookfile']);

        $totalBooks = $books->getRepository()->getTotalRows();
        $itemPerPage = $app['config']->getValue('user_actions_page_size');

        $actions = $app['entity.user-book']->getAvailableLists();

        return $app['twig']->render(
            sprintf('%suser_books_%s.html.twig', $app['config']->getTemplatePrefix(), $action),
            array(
                'books'      => $books,
                'totalBooks' => $totalBooks,
                'pageTitle'  => $app['translator']->trans($actions[$action]),
                'pageNum'    => $page,
                'totalRows'  => $totalBooks,
                'pageCount'  => ceil($totalBooks / $itemPerPage),
            )
        );
    }

    /**
     * Make changes to selection
     *
     * @param Application $app
     * @param string      $action
     *
     * @return string
     */
    public function selectionAction(Application $app, $action)
    {
        $user = $app['security']->getToken()->getUser();

        $modifiedBooks = $app['collection.user-book'];

        $userBooks = $app['collection.user-book']
            ->findFromUserIdAndAction($user->getId(), $action);

        foreach ($app['request']->get('book_id', array()) as $bookId) {

            $userBook = $app['entity.user-book'];

            $userBook->setUserId($user->getId())
                ->setBookId($bookId)
                ->setAction($action);

            // Check book is in the loaded collection
            if ($userBooks->getById($userBook->getId())) {
                $modifiedBooks->add($userBook);
            }
        }

        // Handle download
        if (preg_match('/download_(\w+)/', $app['request']->getContent(), $match)) {
            $archive = $app['factory.archive']->getInstance($match[1]);

            $books = $app['collection.book']
                ->findById($userBooks->getAllBookIds());

            $bookFiles = $app['collection.bookfile']->findFromBooks($books);

            $response = $this->downloadSelection($app, $bookFiles, $archive);
        }

        // Handle removal
        if ($app['request']->get('remove', false)) {
            $modifiedBooks->delete();

            $response = $app->redirect(
                $app['url_generator']->generate('user_books_list', array('action' => $action))
            );
        }


        return $output;
    }

    /**
     * Download selected books
     *
     * @param Application              $app
     * @param BookFileCollection       $bookFiles
     * @param ArchiveAdapterInterface  $archive
     *
     * @return null
     */
    protected function downloadSelection(
        Application $app,
        BookFileCollection $bookFiles,
        ArchiveAdapterInterface $archive
    ) {

        $archive = $archive->setFiles($bookFiles)
            ->generateArchive();

        return $app
            ->sendFile($archive)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'SELECTION'
            );
    }
}
