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
 * Image adapter interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface AdapterInterface
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
    public function generateThumbnail($src, $dst, array $params = array());

    /**
     * Width setter
     *
     * @param int $width
     *
     * @return self
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
     * @return self
     */
    public function setHeight($height);

    /**
     * Height getter
     *
     * @return int
     */
    public function getHeight();
}