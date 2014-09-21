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
use Silex\Application as BaseApplication;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Base resource class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class ResourceAbstract
{
    /**
     * Entity model instance
     * @var Common
     */
    private $entity;

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
     * Constructor
     *
     * @param BaseApplication  $app     Application instance
     * @param EntityAbstract   $entity  Linked model instance
     */
    public function __construct(BaseApplication $app, EntityAbstract $entity)
    {
        $this->app    = $app;
        $this->entity = $entity;
    }

    /**
     * Entity model getter
     *
     * @return Common
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Entity model setter
     *
     * @param  EntityAbstract $entity
     *
     * @return ResourceAbstract
     */
    public function setEntity(EntityAbstract $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get the DB connection instance
     *
     * @return Doctrine\DBAL\Connection
     */
    protected function getConnection()
    {
        return $this->app['db'];
    }

    /**
     * QueryBuilder getter
     */
    protected function getQueryBuilder()
    {
        return $this->getConnection()->createQueryBuilder();
    }

    /**
     * Set data to entity from statement result
     *
     * @param array $result
     *
     * @return Common
     */
    public function setDataFromStatement(array $result)
    {
        $entity = clone($this->getEntity());
        return $entity->setData($result);
    }

    /**
     * Set start offset
     *
     * @param int $offset
     *
     * @return Resource
     */
    public function setFirstResult($offset)
    {
        $this->firstResult = $offset;
        return $this;
    }

    /**
     * Set max number of results
     *
     * @param int $limit
     *
     * @return Resource
     */
    public function setMaxResults($limit)
    {
        $this->maxResults = $limit;
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
        $resetParts = array('select', 'groupBy', 'orderBy')
    ) {
        // Count total rows when using limit
        if ($this->maxResults !== null) {
            $countQuery = clone($queryBuilder);

            $total = (int) $countQuery
                ->resetQueryParts($resetParts)
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
