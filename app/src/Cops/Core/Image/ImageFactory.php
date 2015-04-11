<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Image;

use Cops\Core\AbstractFactory;
use Cops\Core\Image\AdapterInterface;
use Cops\Core\Image\WrongAdapterException;

/**
 * Image factory
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ImageFactory extends AbstractFactory
{
    /**
     * Instance getter
     *
     * @param string $instance
     *
     * @return AdapterInterface
     *
     * @throws WrongAdapterException
     */
    public function getInstance($instance = null)
    {
        $adapter = parent::getInstance($instance);

        if (!$adapter instanceof AdapterInterface) {
            throw new WrongAdapterException(
                'Image adapter must implement \Cops\Core\Image\AdapterInterface'
            );
        }

        return $adapter;
    }
}
