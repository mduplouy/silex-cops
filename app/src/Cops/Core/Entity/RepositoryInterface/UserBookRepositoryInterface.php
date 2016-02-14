<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\RepositoryInterface;

use Cops\Core\InternalRepositoryInterface;
use Cops\Core\Entity\UserBook;


/**
 * User book repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface UserBookRepositoryInterface extends InternalRepositoryInterface
{
    /**
     * Insert row to database
     *
     * @param UserBook $userBook
     *
     * @return bool
     */
    public function insert(UserBook $userBook);

    /**
     * Delete row from database
     *
     * @param UserBook $userBook
     *
     * @return bool
     */
    public function delete(UserBook $userBook);

    /**
     * Find from user ID
     *
     * @param  int $userId
     *
     * @return array
     */
    public function findFromUserId($userId);

    /**
     * Find from user id and action
     *
     * @param int    $userId
     * @param string $action
     *
     * @return array
     */
    public function findFromUserIdAndAction($userId, $action);

    /**
     * Find from book Id and user Id
     *
     * @param int $bookId
     * @param int $userId
     *
     * @return array
     */
    public function findFromBookIdAndUserId($bookId, $userId);
}
