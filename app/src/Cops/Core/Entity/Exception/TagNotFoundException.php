<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\Exception;

/**
 * Exception is thrown when tag cannot be found in DB
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class TagNotFoundException extends \UnexpectedValueException
{
}