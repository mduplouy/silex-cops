<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Exception\Search;

/**
 * Exception is thrown when search adapter can't be used (class missing)
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AdapterException extends \UnexpectedValueException
{
}