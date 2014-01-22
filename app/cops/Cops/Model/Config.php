<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

/**
 * Simple configuration class with hardcoded default values and override by ini file
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Config
{
    /**
     * Default config values
     *
     * @var array
     */
    private $_configValues = array(
        // common
        'last_added'            => 10,

        // web
        'theme'                 => 'default',
        'mobile_theme'          => 'mobile',

        // path
        'public_dir'            => 'web',
        'data_dir'              => 'data',

        // email
        'sender'                => 'php',

        // misc
        'debug'                 => false,
        'use_rewrite'           => false,

        // cover
        'image_processor'       => 'gd',
        'cover_width'           => 320,
        'cover_height'          => 240,

        // tags
        'homepage_tags'         => 30,
        'book_per_page'         => 30,
    );

    /**
     * Template prefix used for mobile rendering
     *
     * @var string
     */
    protected $templatePrefix = '';

    /**
     * Constructor
     *
     * @param string $configFilePath
     *
     * @return void
     */
    public function __construct($configFilePath)
    {
         $confValues = parse_ini_file($configFilePath, false);
         if (is_array($confValues)) {
            $this->_configValues = array_merge($this->_configValues, $confValues);
         }
    }

    /**
     * Get a configuration value from a key
     *
     * @param string $confKey
     *
     * @return mixed
     */
    public function getValue($confKey)
    {
        $confValue = null;
        if (isset($this->_configValues[$confKey])) {
            $confValue = $this->_configValues[$confKey];
        } else {
            throw new \InvalidArgumentException(sprintf("Config value %s doest not exist", $confKey));
        }
        return $confValue;
    }

    /**
     * Set a configuration value
     *
     * @param string $confKey
     *
     * @param mixed $confValue
     *
     * @return Config
     */
    public function setValue($confKey, $confValue)
    {
        $this->_configValues[$confKey] = $confValue;
        return $this;
    }

    /**
     * Template prefix setter
     *
     * @param  string
     *
     * @return Config
     */
     public function setTemplatePrefix($value)
     {
         if (substr($value, -1) != DS) {
             $value .= DS;
         }
         $this->templatePrefix = $value;
         return $this;
     }

    /**
     * Template prefix getter
     *
     * @return string
     */
     public function getTemplatePrefix()
     {
         return $this->templatePrefix;
     }
}
