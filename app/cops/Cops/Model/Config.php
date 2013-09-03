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
    protected $_configValues = array(
        'theme' => 'default',
        'data_dir' => 'data',
    );

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
        }
        return $confValue;
    }
}