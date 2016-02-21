<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity;

/**
 * User book interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface UserBookInterface
{
    /**
     * Get book ID
     *
     * @return int
     */
    public function getBookId();

    /**
     * Get action
     *
     * @return string
     */
    public function getAction();
}
