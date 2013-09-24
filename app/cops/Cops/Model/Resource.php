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
        return \Cops\Model\Core::getDb();
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
}
