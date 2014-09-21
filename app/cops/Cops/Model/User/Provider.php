<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Silex\Application as BaseApplication;
use Cops\Model\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User provider for security component
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Provider implements UserProviderInterface
{
    /**
     * User instance
     * @var User
     */
    private $user;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     *@inheritDoc
     */
    public function loadUserByUsername($username)
    {
        return $this->user->loadByUsername($username);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        return $class == get_class($this->user);
    }
}