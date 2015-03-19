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
 * GD Image processing class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Gd extends AbstractAdapter implements AdapterInterface
{
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
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('Please install gd module before using it');
        }
        /** @codeCoverageIngoreEnd */

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

        $sourceInfo = getimagesize($src);

        $sourceImage = imagecreatefromjpeg($src);
        $targetImage = imagecreatetruecolor($this->getWidth(), $this->getHeight());

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $this->getWidth(),
            $this->getHeight(),
            $sourceInfo[0],
            $sourceInfo[1]
        );

        imagejpeg($targetImage, $dest, self::DEFAULT_QUALITY);
    }
}
