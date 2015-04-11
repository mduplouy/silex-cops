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
 * Use repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface UseRepositoryInterface
{
    /**
     * Set repository closure
     *
     * @param \Closure
     *
     * @return self
     */
    public function setRepositoryClosure(\Closure $closure);

    /**
     * Get repository closure
     *
     * @return \Closure
     */
    public function getRepositoryClosure();

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository();
}
