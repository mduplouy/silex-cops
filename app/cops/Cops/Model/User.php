<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

use Cops\Model\EntityAbstract;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * User entity class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class User extends EntityAbstract implements UserInterface, \Serializable
{
    /**
     * User roles
     */
    const ROLE_USER   = 'ROLE_USER';
    const ROLE_EDIT   = 'ROLE_EDIT';
    const ROLE_ADMIN  = 'ROLE_ADMIN';

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
     * @var string
     */
    private $roles = array(self::ROLE_USER);

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
     * Get all roles as array
     *
     * @return array
     */
    public function getAllRoles()
    {
        return array(
            self::ROLE_USER  => self::ROLE_USER,
            self::ROLE_EDIT  => self::ROLE_EDIT,
            self::ROLE_ADMIN => self::ROLE_ADMIN,
        );
    }

    /**
     * Load single user
     *
     * @param  int $userId
     *
     * @return User
     */
    public function load($userId)
    {
        $result = $this->getResource()->load($userId);

        $this->setData($result);

        return $this;
    }

    /**
     * Load by username
     *
     * @param  string $username
     *
     * @return User
     */
    public function loadByUsername($username)
    {
        if (!$userData = $this->getResource()->loadByUsername($username, $this)) {
            throw new UsernameNotFoundException(sprintf('User %s not found', $username));
        }

        $this->setId($userData['id'])
            ->setUsername($userData['username'])
            ->setPassword($userData['password'])
            ->setRoles($userData['roles']);

        return $this;
    }

    /**
     * Save user
     *
     * @return int
     */
    public function save()
    {
        $resource = $this->getResource();

        if ($userId = $this->getId()) {
            $resource->update();
        } else {
            $userId = $resource->insert();
        }

        return $userId;
    }

    /**
     * Delete user
     *
     * @return void
     */
    public function delete()
    {
        $this->getResource()->delete();
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
    }
}