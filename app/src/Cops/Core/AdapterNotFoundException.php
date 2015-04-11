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
 * Exception thrown by factory when instance type is not found
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AdapterNotFoundException extends \InvalidArgumentException
{
}
