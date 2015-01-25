<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\ImageProcessor\Adapter;

use Silex\Application;
use Cops\Model\Core;
use Cops\Model\ImageProcessor\ImageProcessorAbstract;
use Cops\Model\ImageProcessor\ImageProcessorInterface;
use Cops\Exception\ImageProcessor\AdapterException;


/**
 * GD Image processing class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Gd extends ImageProcessorAbstract implements ImageProcessorInterface
{
    /**
     * Image resource
     * @var \resource
     */
    protected $_image;

    /**
     * Default image quality
     * @var string
     */
    const DEFAULT_QUALITY = 80;

    /**
     * Constructor
     *
     * @param Application $app
     *
     * @throws \Cops\Exception\ImageProcessor\AdapterException
     */
    public function __construct(Application $app)
    {
        if (!extension_loaded('gd')) {
            throw new AdapterException('Please install php5-gd before using it');
        }

        parent::__construct($app);
    }

    /**
     * Generate a thumbnail for image
     *
     * @param string $src    The source image file path
     * @param string $dest   The target image file path
     * @param array  $params Options
     */
    public function generateThumbnail($src, $dest, array $params=array())
    {
        $sourceInfo = getimagesize($src);

        if (isset($params['width'])) {
            $this->setWidth($params['width']);
        }
        if (isset($params['height'])) {
            $this->setHeight($params['height']);
        }

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
