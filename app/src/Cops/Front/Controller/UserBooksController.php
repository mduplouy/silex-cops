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
use Cops\Core\Entity\UserBookCollection;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Cops\Front\EmptySelectionException;

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

        $actions = $app['entity.user-book']->getAvailableLists();
        $pageTitle = $app['translator']->trans($actions[$action]);

        if (!$userBooks->count()) {
            return $app['twig']->render(
                sprintf('%suser_books_empty.html.twig', $app['config']->getTemplatePrefix()),
                array(
                    'pageTitle'  => $pageTitle,
                )
            );
        }

        return $this->displayListAction($app, $userBooks, $action, $page, $pageTitle);
    }

    /**
     * Display list action
     *
     * @param Application        $app
     * @param UserBookCollection $userBooks
     * @param string             $action
     * @param int                $page
     * @param string             $pageTitle
     *
     * @return string
     */
    protected function displayListAction(Application $app, UserBookCollection $userBooks, $action, $page, $pageTitle)
    {
        $itemsPerPage = $app['config']->getValue('user_actions_page_size');

        $books = $app['collection.book']
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->findById($userBooks->getAllBookIds())
            ->addAuthors($app['collection.author'])
            ->addBookFiles($app['collection.bookfile']);

        $totalBooks = $books->getRepository()->getTotalRows();

        return $app['twig']->render(
            sprintf('%suser_books_%s.html.twig', $app['config']->getTemplatePrefix(), $action),
            array(
                'books'      => $books,
                'totalBooks' => $totalBooks,
                'pageTitle'  => $pageTitle,
                'pageNum'    => $page,
                'totalRows'  => $totalBooks,
                'pageCount'  => ceil($totalBooks / $itemsPerPage),
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
        $userId = $app['security']->getToken()->getUser()->getId();

        try {
            $response = $this->handleSelection($app, $userId, $action);
        } catch (EmptySelectionException $e) {
            $response = $app->redirect(
                $app['url_generator']->generate('user_books_list', array('action' => $action))
            );
        }

        return $response;
    }

    /**
     * Handle book selection
     *
     * @param Application  $app
     * @param int          $userId
     * @param string       $action
     *
     * @return mixed
     *
     * @throws EmptySelectionException
     *
     */
    protected function handleSelection(Application $app, $userId, $action)
    {
        $userBooks = $app['collection.user-book']
            ->findFromUserIdAndAction($userId, $action);

        $modifiedBooks = $app['collection.user-book'];

        foreach ($app['request']->get('book_id', array()) as $bookId) {
            // Check book is in the loaded collection
            if ($modifiedBook = $userBooks->getBook($userId, $bookId, $action)) {
                $modifiedBooks->add($modifiedBook);
            }
        }

        switch (true) {

            case $modifiedBooks->count() == 0:
                throw new EmptySelectionException('No books selected');

            case preg_match('/download_(\w+)/', $app['request']->getContent(), $match) > 0:
                return $this->downloadSelection($app, $userBooks, $match[1]);

            case $app['request']->get('remove', false):
                $modifiedBooks->delete();
                // Do not break to redirect after deletion

            default:
                throw new EmptySelectionException('Nothing to do');
        }
    }

    /**
     * Download selected books
     *
     * @param Application         $app
     * @param UserBookCollection  $userBooks
     * @param string              $archiveType
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function downloadSelection(Application $app, UserBookCollection $userBooks, $archiveType)
    {
        $downloadBooks = $app['collection.book']
                ->findById($userBooks->getAllBookIds());

        $bookFiles = $app['collection.bookfile']->findFromBooks($downloadBooks);

        $archive = $app['factory.archive']->getInstance($archiveType);

        $archiveFile = $archive->setFiles($bookFiles)
            ->generateArchive();

        return $app
            ->sendFile($archiveFile)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                sprintf('selection%s', $archive->getExtension())
            );
    }
}
