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

use Cops\Core\Image\AdapterInterface;
use Cops\Core\Config;

/**
 * Abstract image adapter
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Default image quality
     * @var string
     */
    const DEFAULT_QUALITY = 80;

    /**
     * Config instance
     * @var Config
     */
    protected $config;

    /**
     * Image width
     * @var int
     */
    protected $width;

    /**
     * Image height
     * @var int
     */
    protected $height;

    /**
     * Constructor
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->width  = (int) $this->config->getValue('cover_width');
        $this->height = (int) $this->config->getValue('cover_height');
    }

    /**
     * Set size from params
     *
     * @param array $params
     *
     * @return void
     */
    protected function setSizeFromParams(array $params)
    {
        if (array_key_exists('width', $params)) {
            $this->setWidth($params['width']);
        }

        if (array_key_exists('height', $params)) {
            $this->setHeight($params['height']);
        }
    }

    /**
     * Width setter
     *
     * @param int $width
     *
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = (int) $width;

        return $this;
    }

    /**
     * Width getter
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Height setter
     *
     * @param int $height
     *
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = (int) $height;

        return $this;
    }

    /**
     * Height getter
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
