<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Exception\ImageProcessor;

/**
 * Exception is thrown when image processor adapter can't be used (ie gd not installed)
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AdapterException extends \RuntimeException
{
}