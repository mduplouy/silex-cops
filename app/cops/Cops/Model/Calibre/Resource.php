<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Calibre;

use Cops\Model\ResourceAbstract;
use Doctrine\DBAL\Connection;
use PDO;

/**
 * Calibre resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends ResourceAbstract
{
    /**
     * Database trigger description
     */
    const TRIGGER_BOOK_INSERT = 'Trigger on book insert';
    const TRIGGER_BOOK_UPDATE = 'Trigger on book update';

    /**
     * Database Trigger names
     */
    const TRIGGER_BOOK_INSERT_NAME = 'books_insert_trg';
    const TRIGGER_BOOK_UPDATE_NAME = 'books_update_trg';

    /**
     * Database trigger SQL
     */
    const TRIGGER_BOOK_INSERT_SQL = 'CREATE TRIGGER books_insert_trg AFTER INSERT ON books
        BEGIN
            UPDATE books SET sort=title_sort(NEW.title),uuid=uuid4() WHERE id=NEW.id;
        END';
    const TRIGGER_BOOK_UPDATE_SQL = 'CREATE TRIGGER books_update_trg AFTER UPDATE ON books
        BEGIN
            UPDATE books SET sort=title_sort(NEW.title)
                WHERE id=NEW.id AND OLD.title <> NEW.title;
        END';

    /**
     * Trigger list
     * @var array
     */
    private $triggers;

    /**
     * Connection instance
     * @var Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * Constructor
     *
     * @param \Doctrine\DBAL\Connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->triggers = array(
            self::TRIGGER_BOOK_INSERT_NAME => array(
                'sql'  => self::TRIGGER_BOOK_INSERT_SQL,
                'desc' => self::TRIGGER_BOOK_INSERT,
            ),
            self::TRIGGER_BOOK_UPDATE_NAME => array(
                'sql'  => self::TRIGGER_BOOK_UPDATE_SQL,
                'desc' => self::TRIGGER_BOOK_UPDATE,
            ),
        );
    }

    /**
     * Try to load triggers from database
     *
     * @return array
     */
    public function loadTriggersFromDb()
    {
        return
            $this->connection
            ->createQueryBuilder()
            ->select('name')
            ->from('SQLite_Master', 'main')
            ->where('name IN(:trigger_name)')
            ->setParameter('trigger_name', array_keys($this->triggers), Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the list of predefined triggers
     *
     * @return array
     */
    public function getTriggers()
    {
        return $this->triggers;
    }

    /**
     * Drop a trigger from database
     *
     * @param string $triggerName
     *
     * @return Doctrine\DBAL\Driver\Statement
     */
    public function dropTrigger($triggerName)
    {
        $this->checkTrigger($triggerName);
        // Doesn't seem available in doctrine dbal
        $sql = sprintf('DROP TRIGGER "%s"', $triggerName);
        return $this->connection->executeQuery($sql);
    }

    /**
     * Restore a trigger into database
     *
     * @param string $triggerName
     *
     * @return Doctrine\DBAL\Driver\Statement
     */
    public function restoreTrigger($triggerName)
    {
        $this->checkTrigger($triggerName);
        // Doesn't seem available in doctrine dbal
        return $this->connection->executeQuery($this->triggers[$triggerName]['sql']);
    }

    /**
     * Get trigger type or throw exception
     *
     * @param string $triggerType
     *
     * @return void
     *
     * @throws \UnexpectedValueException
     */
    private function checkTrigger($triggerName)
    {
        if (!array_key_exists($triggerName, $this->triggers)) {
            throw new \UnexpectedValueException(
                sprintf('Trigger type %s does not exist', $triggerType)
            );
        }
    }
}
