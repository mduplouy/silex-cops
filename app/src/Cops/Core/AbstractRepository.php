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

use Cops\Core\AbstractApplicationAware;
use Cops\Core\RepositoryInterface;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Repository abstract class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractRepository extends AbstractApplicationAware implements RepositoryInterface
{
    /**
     * Offset for queries
     * @var int
     */
    protected $firstResult;

    /**
     * Limit for queries
     * @var int
     */
    protected $maxResults;

    /**
     * Total row count when using limit
     * @var int
     */
    protected $totalRows;

    /**
     * Get the DB connection instance
     *
     * @return Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->app['db'];
    }

    /**
     * QueryBuilder getter
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getConnection()->createQueryBuilder();
    }

    /**
     * Set start offset
     *
     * @param int $offset
     *
     * @return self
     */
    public function setFirstResult($offset)
    {
        $this->firstResult = (int) $offset;
        return $this;
    }

    /**
     * Set max number of results
     *
     * @param int $limit
     *
     * @return \Cops\Resource
     */
    public function setMaxResults($limit)
    {
        $this->maxResults = (int) $limit;
        return $this;
    }

    /**
     * Get total number of rows, ignoring limit clause
     *
     * @return int
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * Paginate query*
     *
     * @param  QueryBuilder $queryBuilder
     * @param  array        $resetParts
     *
     * @return QueryBuilder
     */
    protected function paginate(
        QueryBuilder $queryBuilder,
        array $resetParts = array('select', 'groupBy', 'orderBy')
    ) {
        // Count total rows when using limit
        if ($this->maxResults !== null) {
            $countQuery = clone($queryBuilder);

            $total = (int) $countQuery->resetQueryParts($resetParts)
                ->select('COUNT(*)')
                ->execute()
                ->fetchColumn();

            $this->totalRows = $total;

            $queryBuilder->setFirstResult($this->firstResult)
                ->setMaxResults($this->maxResults);
        }

        return $queryBuilder;
    }
}
