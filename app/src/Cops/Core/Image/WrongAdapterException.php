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

/**
 * Exception thrown by image factory when adapter is not correct
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class WrongAdapterException extends \InvalidArgumentException
{
}
