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
use Cops\Core\Config;
use Doctrine\DBAL\Schema\Table;
use PDO;
use Cops\Core\Entity\UserBook;
use Cops\Core\Entity\RepositoryInterface\UserBookRepositoryInterface;

/**
 * User book repository
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserBookRepository extends AbstractInternalRepository implements UserBookRepositoryInterface
{
    /**
     * Resource table name
     */
    const TABLE_NAME = 'user_books';

    /**
     * Config instance
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Insert row to database
     *
     * @param UserBook $userBook
     *
     * @return bool
     */
    public function insert(UserBook $userBook)
    {
        return $this->modifyRow($userBook, 'insert');
    }

    /**
     * Delete row from database
     *
     * @param UserBook $userBook
     *
     * @return bool
     */
    public function delete(UserBook $userBook)
    {
        return $this->modifyRow($userBook, 'delete');
    }

    /**
     * Modify row
     *
     * @param UserBook $userBook
     * @param string   $action
     */
    private function modifyRow(UserBook $userBook, $action)
    {
        return $this->getConnection()
            ->$action(
                self::TABLE_NAME,
                array(
                    'book_db' => $this->config->getValue('current_database_key'),
                    'user_id' => $userBook->getUserId(),
                    'book_id' => $userBook->getBookId(),
                    'action'  => $userBook->getAction(),
                ),
                array(
                    PDO::PARAM_STR,
                    PDO::PARAM_INT,
                    PDO::PARAM_INT,
                    PDO::PARAM_STR,
                )
            );
    }

    /**
     * Get default table structure
     *
     * @return Table
     */
    public function getTableStructure()
    {
        $table = new Table(self::TABLE_NAME);
        $table->addColumn('book_db', 'string', array('length' => 50));
        $table->addColumn('user_id', 'integer', array('unsigned' => true));
        $table->addColumn('book_id', 'integer', array('unsigned' => true));
        $table->addColumn('action',  'string',  array('length' => 10));
        $table->addUniqueIndex(array('book_db', 'user_id', 'book_id', 'action'), 'uniq_user_books_action');
        return $table;
    }

    /**
     * Find from user ID
     *
     * @param  int $userId
     *
     * @return array
     */
    public function findFromUserId($userId)
    {
        return $this->getBaseSelect()
            ->setParameter('user_id', $userId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll();
    }

    /**
     * Find from user id and action
     *
     * @param int    $userId
     * @param string $action
     *
     * @return array
     */
    public function findFromUserIdAndAction($userId, $action)
    {
        return $this->getBaseSelect()
            ->andWhere('main.action = :action')
            ->setParameter('user_id', $userId, PDO::PARAM_INT)
            ->setParameter('action',  $action, PDO::PARAM_STR)
            ->execute()
            ->fetchAll();
    }

    /**
     * Find from book Id and user Id
     *
     * @param int $bookId
     * @param int $userId
     *
     * @return array
     */
    public function findFromBookIdAndUserId($bookId, $userId)
    {
        return $this->getBaseSelect()
            ->andWhere('main.book_id = :book_id')
            ->setParameter('user_id', $userId, PDO::PARAM_INT)
            ->setParameter('book_id', $bookId, PDO::PARAM_INT)
            ->execute()
            ->fetchAll();
    }

    /**
     * Get the base select from QueryBuilder
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getBaseSelect()
    {
        return $this->getQueryBuilder()
            ->select(
                'main.user_id',
                'main.action',
                'main.book_id'
            )
            ->from(self::TABLE_NAME, 'main')
            ->where('main.book_db = :book_db')
            ->setParameter(
                'book_db',
                $this->config->getValue('current_database_key'),
                PDO::PARAM_STR
            )
            ->andWhere('main.user_id = :user_id');
    }
}
