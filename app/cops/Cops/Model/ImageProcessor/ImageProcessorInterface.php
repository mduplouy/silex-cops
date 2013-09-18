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
    /**
     * Generate a thumbnail for image
     *
     * @param string $src    The source image file path
     * @param string $dest   The target image file path
     * @param array  $params Options
     *
     * @return void
     */
    public function generateThumbnail($src, $dst, array $params);

    /**
     * Width setter
     *
     * @param int $width
     *
     * @return Cops\Model\ImageProcessor\ImageProcessorInterface
     */
    public function setWidth($width);

    /**
     * Width getter
     *
     * @return int
     */
    public function getWidth();

    /**
     * Height setter
     *
     * @param int $height
     *
     * @return Cops\Model\ImageProcessor\ImageProcessorInterface
     */
    public function setHeight($height);

    /**
     * Height getter
     *
     * @return int
     */
    public function getHeight();
}