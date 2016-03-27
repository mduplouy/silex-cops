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
 * Repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface RepositoryInterface
{
    /**
     * Get the DB connection instance
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection();

    /**
     * QueryBuilder getter
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQueryBuilder();

    /**
     * Set start offset
     *
     * @param int $offset
     *
     * @return self
     */
    public function setFirstResult($offset);

    /**
     * Set max number of results
     *
     * @param int $limit
     *
     * @return self
     */
    public function setMaxResults($limit);

    /**
     * Get total number of rows, ignoring limit clause
     *
     * @return int
     */
    public function getTotalRows();
}
