<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Image\Adapter;

use Cops\Core\Image\AbstractAdapter;
use Cops\Core\Image\AdapterInterface;
use Cops\Core\Config;

/**
 * Imagick processing class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Imagick extends AbstractAdapter implements AdapterInterface
{
    /**
     * Default image quality
     * @var string
     */
    const DEFAULT_QUALITY = 92;

    /**
     * Constructor
     *
     * @param Config $config
     *
     * @throws \RuntimeException
     */
    public function __construct(Config $config)
    {
        /** @codeCoverageIgnoreStart */
        if (!extension_loaded('imagick')) {
            throw new \RuntimeException('Please install imagick module before using it');
        }
        /** @codeCoverageIgnoreEnd */

        parent::__construct($config);
    }

    /**
     * Generate a thumbnail for image
     *
     * @param string $src    The source image file path
     * @param string $dest   The target image file path
     * @param array  $params Options
     *
     * @return void
     */
    public function generateThumbnail($src, $dest, array $params = array())
    {
        $this->setSizeFromParams($params);

        $imagick = new \Imagick($src);
        $imagick->setImageResolution (92, 92);
        $imagick->setImageCompression(\Imagick::COMPRESSION_ZIP);
        $imagick->setImageCompressionQuality(self::DEFAULT_QUALITY);
        $imagick->thumbnailImage(
            $this->getWidth(),
            $this->getHeight(),
            true,
            false
        );
        $imagick->stripImage();
        $imagick->writeImage($dest);
    }
}
