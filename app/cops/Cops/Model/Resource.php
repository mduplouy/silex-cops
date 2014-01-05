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
use Doctrine\DBAL\Driver\PDOStatement;
use Cops\Model\CoreInterface;

/**
 * Base resource class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class Resource
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
     * Base select getter
     *
     * @return string
     */
    public function getBaseSelect()
    {
        return $this->_baseSelect;
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

}
