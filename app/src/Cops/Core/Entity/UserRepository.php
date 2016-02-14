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

use Cops\Core\AbstractInternalRepository;
use Cops\Core\Entity\RepositoryInterface\UserRepositoryInterface;
use Cops\Core\Entity\User;
use PDO;
use Doctrine\DBAL\Schema\Table;

/**
 * User resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserRepository extends AbstractInternalRepository implements UserRepositoryInterface
{
    /**
     * Resource table name
     */
    const TABLE_NAME = 'user';

    /**
     * Save user
     *
     * @return int
     */
    public function save(User $user)
    {
        if ($user->getId()) {
            $this->update($user);
        } else {
            $this->insert($user);
        }

        return $user->getId();
    }

    /**
     * Insert user data
     *
     * @param  User $user
     *
     * @return void
     */
    protected function insert(User $user)
    {
        $con = $this->getConnection();
        $con->insert(self::TABLE_NAME,
            array(
                'username' => $user->getUsername(),
                'password' => $this->getEncoder()->encodePassword($user->getPassword(), ''),
                'roles'    => $user->getRole(),
            ),
            array(
                PDO::PARAM_STR,
                PDO::PARAM_STR,
                PDO::PARAM_STR,
            )
        );

        $user->setId($con->lastInsertId());
    }

    /**
     * Update user data
     *
     * @param  User $user
     *
     * @return void
     */
    protected function update(User $user)
    {
        $qb = $this->getQueryBuilder()
            ->update(self::TABLE_NAME)
            ->set('username', ':username')
            ->set('roles', ':roles')
            ->where('id = :id')
            ->setParameter('username', $user->getUsername(), PDO::PARAM_STR)
            ->setParameter('roles',    $user->getRole(),     PDO::PARAM_STR)
            ->setParameter('id',       $user->getId(),       PDO::PARAM_INT);

        if ($password = $user->getPassword()) {
            $qb->set('password', ':password')
                ->setParameter(
                    'password',
                    $this->getEncoder()->encodePassword($password, ''),
                    PDO::PARAM_STR
                );
        }

        $qb->execute();
    }

    /**
     * Delete user data
     *
     * @param  User $user
     *
     * @return bool
     */
    public function delete(User $user)
    {
        return (bool) $this->getConnection()
            ->delete(self::TABLE_NAME,
            array(
                'id' => $user->getId(),
            ),
            array(
                PDO::PARAM_INT,
            )
        );
    }

    /**
     * Find by id
     *
     * @param  int $userId
     *
     * @return array
     */
    public function findById($userId)
    {
        return $this->getQueryBuilder()
            ->select('main.*')
            ->from(self::TABLE_NAME, 'main')
            ->where('id = :id')
            ->setParameter('id', $userId, PDO::PARAM_INT)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find by username
     *
     * @param  string $username
     *
     * @return array
     */
    public function findByUsername($username)
    {
        return $this->getQueryBuilder()
            ->select('main.*')
            ->from(self::TABLE_NAME, 'main')
            ->where('username = :username')
            ->setParameter('username', $username, PDO::PARAM_STR)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find all
     *
     * @return array
     */
    public function findAll()
    {
        return $this->getQueryBuilder()
            ->select('main.*')
            ->from(self::TABLE_NAME, 'main')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create table
     *
     * @return bool
     */
    public function createTable()
    {
        if ($return = parent::createTable()) {
            $this->insertDefaultValues();
        }

        return $return;
    }

    /**
     * Get default table structure
     *
     * @return Table
     */
    public function getTableStructure()
    {
        $table = new Table(self::TABLE_NAME);
        $table->addColumn('id',       'integer', array('autoincrement' => true, 'unsigned' => true));
        $table->addColumn('username', 'string',  array('length' => 32));
        $table->addColumn('password', 'string',  array('length' => 100));
        $table->addColumn('roles',    'string',  array('length' => 20));
        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('username'), 'uniq_username');

        return $table;
    }

    /**
     * Insert default value into table
     *
     * @return $this
     */
    private function insertDefaultValues()
    {
        $encryptedPassword = $this->app['security.encoder.digest']->encodePassword(
            $this->app['config']->getValue('default_password'),
            ''
        );

        $con = $this->getConnection();
        $con->insert(self::TABLE_NAME,
            array(
                'username' => $this->app['config']->getValue('default_login'),
                'password' => $encryptedPassword,
                'roles'    => \Cops\Core\Entity\User::ROLE_ADMIN,
            ),
            array(
                PDO::PARAM_STR,
                PDO::PARAM_STR,
                PDO::PARAM_STR,
            )
        );

        return $this;
    }

    /**
     * Get encoder for password operations
     *
     * @return Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder
     */
    public function getEncoder()
    {
        return $this->app['security.encoder.digest'];
    }
}
