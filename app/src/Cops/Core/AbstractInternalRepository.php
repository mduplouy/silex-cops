<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core;

/**
 * Internal Repository abstract class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractInternalRepository extends AbstractRepository implements InternalRepositoryInterface
{
    /**
     * Get the DB connection instance
     * Overloaded to use internal connection
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->app['dbs']['silexCops'];
    }

    /**
     * Create table
     *
     * @return bool
     */
    public function createTable()
    {
        $schema = $this->getConnection()->getSchemaManager();

        $created = false;
        if (!$schema->tablesExist(array($this->getTableName()))) {
            $schema->createTable($this->getTableStructure());
            $created = true;
        }

        return $created;
    }

    /**
     * Drop table
     *
     * @return $this
     */
    public function dropTable()
    {
        $schema = $this->getConnection()->getSchemaManager();

        if ($schema->tablesExist(array($this->getTableName()))) {
            $this->getConnection()
                ->getSchemaManager()
                ->dropTable($this->getTableName());
        }

        return $this;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName()
    {
        return static::TABLE_NAME;
    }
}
