<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Archive;

use Cops\Core\AbstractFactory;
use Cops\Core\Archive\AdapterInterface;
use Cops\Core\Archive\WrongAdapterException;

/**
 * Archive factory
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ArchiveFactory extends AbstractFactory
{
    /**
     * Instance getter
     *
     * @return \Cops\Core\Archive\AdapterInterface
     *
     * @throws WrongAdapterInterface
     */
    public function getInstance($instance = null)
    {
        $adapter = parent::getInstance($instance);

        if (!$adapter instanceof AdapterInterface) {
            throw new WrongAdapterException(
                'Archive adapter must implement \Cops\Core\Archive\AdapterInterface'
            );
        }

        return $adapter;
    }
}
