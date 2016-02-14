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
use Cops\Core\Entity\User;


/**
 * User repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface UserRepositoryInterface extends InternalRepositoryInterface
{
    /**
     * Save user
     *
     * @return int
     */
    public function save(User $user);

    /**
     * Delete user data
     *
     * @param  User $user
     *
     * @return bool
     */
    public function delete(User $user);

    /**
     * Find by id
     *
     * @param  int $userId
     *
     * @return array
     */
    public function findById($userId);

    /**
     * Find by username
     *
     * @param  string $username
     *
     * @return array
     */
    public function findByUsername($username);

    /**
     * Find all
     *
     * @return array
     */
    public function findAll();
}
