<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Cops\Core\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

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
     * @param User $user
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
        return $this->user->findByUsername($username);
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
