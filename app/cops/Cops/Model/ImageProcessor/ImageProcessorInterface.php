<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\ImageProcessor;

/**
 * Image processing interface
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface ImageProcessorInterface
{

    public function generateThumbnail($src, $dst, array $params);

    public function setWidth($width);

    public function setHeight($height);

    public function resize($width=null, $height=null);

}