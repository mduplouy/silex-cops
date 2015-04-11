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
 * Internal Repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface InternalRepositoryInterface extends RepositoryInterface
{
    /**
     * Create table
     *
     * @return bool True if table was created
     */
    public function createTable();

    /**
     * Drop table
     *
     * @return void
     */
    public function dropTable();

    /**
     * Get table structure
     *
     * @return \Doctrine\DBAL\Schema\Table
     */
    public function getTableStructure();

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName();
}
