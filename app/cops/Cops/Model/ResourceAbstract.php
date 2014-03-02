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
     * Get the DB connection instance
     *
     * @return Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return Core::getDb();
    }

    /**
     * Base QB select getter
     *
     * @deprecated
     *
     * @return Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getBaseSelect()
    {
        return $this->getQueryBuilder();
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
}
