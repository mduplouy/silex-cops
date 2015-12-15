<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity;

use Cops\Core\AbstractEntity;
use Cops\Core\CollectionableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Cops\Core\Entity\UserBookCollection;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Cops\Core\Entity\Exception\UserNotFoundException;

/**
 * User entity
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class User extends AbstractEntity implements CollectionableInterface, UserInterface, \Serializable
{
    /**
     * User roles
     */
    const ROLE_USER         = 'ROLE_USER';
    const ROLE_USER_ACTIONS = 'ROLE_USER_ACTIONS';
    const ROLE_EDIT         = 'ROLE_EDIT';
    const ROLE_ADMIN        = 'ROLE_ADMIN';

    /**
     * Id
     * @var int
     */
    private $id;

    /**
     * Username
     * @var string
     */
    private $username;

    /**
     * Password
     * @var string
     */
    private $password;

    /**
     * Role
     * @var array
     */
    private $roles = array(self::ROLE_USER);

    /**
     * User books
     * @var UserBookCollection
     */
    private $userBooks;

    /**
     * Constructor
     *
     * @param UserBookCollection $userBooks
     */
    public function __construct(UserBookCollection $userBooks)
    {
        $this->userBooks = $userBooks;
    }

    /**
     * Find by username
     *
     * @param  string $username
     *
     * @return $this
     */
    public function findByUsername($username)
    {
        $result = $this->getRepository()->findByUserName($username);

        if (empty($result)) {
            throw new UsernameNotFoundException(sprintf('User %s not found', $username));
        }

        return $this->setDataFromArray($result);
    }

    /**
     * Find by id
     *
     * @param  int $userId
     *
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function findById($userId)
    {
        $result = $this->getRepository()->findById($userId);

        if (empty($result)) {
            throw new UserNotFoundException(sprintf('User width id %s not found', $userId));
        }

        return $this->setDataFromArray($result);
    }

    /**
     * Save user
     *
     * @return int
     */
    public function save()
    {
        return $this->getRepository()->save($this);
    }

    /**
     * Delete user
     *
     * @return bool
     */
    public function delete()
    {
        return $this->getRepository()->delete($this);
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->roles,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->roles
        ) = unserialize($serialized);

        return $this;
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set roles
     *
     * @param mixed $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = array($roles);
        }

        $this->roles = $roles;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return current($this->roles);
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->roles = array($role);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get roles as string
     *
     * @return string
     */
    public function getRolesAsString($sep = ', ')
    {
        return implode($sep, $this->roles);
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->password = null;
        return $this;
    }

    /**
     * Get admin role
     *
     * @return string
     */
    public function getAdminRole()
    {
        return self::ROLE_ADMIN;
    }

    /**
     * Get user role
     *
     * @return string
     */
    public function getUserRole()
    {
        return self::ROLE_USER;
    }

    /**
     * Get edit role
     *
     * @return string
     */
     public function getEditRole()
     {
         return self::ROLE_EDIT;
     }

    /**
     * Get user actions role
     *
     * @return string
     */
     public function getUserACtionsRole()
     {
         return self::ROLE_USER_ACTIONS;
     }


    /**
     * Get all roles as array
     *
     * @return array
     */
    public function getAllRoles()
    {
        return array(
            self::ROLE_USER          => self::ROLE_USER,
            self::ROLE_USER_ACTIONS  => self::ROLE_USER_ACTIONS,
            self::ROLE_EDIT          => self::ROLE_EDIT,
            self::ROLE_ADMIN         => self::ROLE_ADMIN,
        );
    }

    /**
     * Get user books
     *
     * @return \Cops\Core\Entity\UserBookCollection
     */
    public function getUserBooks()
    {
        return $this->userBooks;
    }

    /**
     * Load user books from book Id
     *
     * @param int $bookId
     *
     * @return \Cops\Core\Entity\UserBookCollection
     */
    public function findUserBooksFromBookId($bookId)
    {
        return $this->userBooks->findFromBookIdAndUserId($bookId, $this->getId());
    }
}
