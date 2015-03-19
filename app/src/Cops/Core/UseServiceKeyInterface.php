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
 * Use service key interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface UseServiceKeyInterface
{
    /**
     * Set service key for collection operations
     *
     * @param string $key
     *
     * @return self
     */
    public function setServiceKey($key);

    /**
     * Get service key
     *
     * @return string
     */
    public function getServiceKey();
}
