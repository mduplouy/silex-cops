<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cops\Core\Provider\Database;

/**
 * MySQL database service provider
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class MysqlProvider extends AbstractDoctrineProvider
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct('pdo_mysql');
    }
}
