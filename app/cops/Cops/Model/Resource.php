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
use \Doctrine\DBAL\Driver\PDOStatement;
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
     * Add items to collection
     *
     * @param  CoreInterface $object
     * @param  PDOStatement  $stmt
     *
     * @return Collection
     */
    protected function _feedCollection(CoreInterface $object, PDOStatement $stmt)
    {
        $collection = $object->getCollection();

        foreach($stmt as $result) {
            $myObject = clone($object);

            $myObject->setData($result);
            $collection->add($myObject);
        }

        return $collection;
    }
}
