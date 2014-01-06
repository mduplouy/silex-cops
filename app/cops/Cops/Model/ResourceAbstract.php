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

use Cops\Model\Core;
use Cops\Model\Common;
use Cops\Model\CoreInterface;

/**
 * Base resource class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class ResourceAbstract
{
    /**
     * Base select statement for loading
     * @var string
     */
    protected $_baseSelect;

    /**
     * Entity model instance
     * @var Common
     */
    private $_entity;

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
     */
    public function __construct(Common $entity)
    {
        $this->_entity = $entity;
    }

    /**
     * Model getter
     *
     * @param string $model
     * @return Common
     */
    public function getModel($model)
    {
        $app = Core::getApp();
        return $app['core']->getModel($model);
    }

    /**
     * Entity model getter
     *
     * @return Cops\Model\Common
     */
    public function getEntity()
    {
        return $this->_entity;
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
     * @return Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getBaseSelect()
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
    public function setDataFromStatement($result)
    {
        $entity = clone($this->getEntity());
        return $entity->setData($result);
    }

    public function setFirstResult($offset)
    {
        $this->firstResult = $offset;
        return $this;
    }

    public function setMaxResults($limit)
    {
        $this->maxResults = $limit;
        return $this;
    }

    public function setTotalRows($total)
    {
        $this->totalRows = $total;
    }
}
