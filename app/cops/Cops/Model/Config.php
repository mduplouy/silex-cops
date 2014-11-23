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

use Cops\Model\Utils;

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
    private $configValues = array(
        // common
        'last_added'              => 10,

        // web
        'theme'                   => 'default',
        'mobile_theme'            => 'mobile',

        // path
        'public_dir'              => 'web',
        'data_dir'                => array('data'),
        'internal_db'             => 'data/silexCops',

        // email
        'sender'                  => 'php',

        // misc
        'debug'                   => false,
        'use_rewrite'             => false,

        // cover
        'image_processor'         => 'gd',
        'cover_width'             => 320,
        'cover_height'            => 240,

        // tags
        'homepage_tags'           => 30,
        'book_per_page'           => 30,

        // admin
        'path'                    => '/admin',
        'inline_edit'             => false,

        // calibre
        'author_sort_copy_method' => 'invert',
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
     * @param Utils  $stringUtils
     */
    public function __construct($configFilePath, Utils $stringUtils)
    {
        $confValues = parse_ini_file($configFilePath, false);
        if (is_array($confValues)) {
            $this->configValues = array_merge($this->configValues, $confValues);
        }

        if (!is_array($this->configValues['data_dir'])) {
            $this->configValues['data_dir'] = array('default' => $this->configValues['data_dir']);
        }

        // Sanitize db key to use it in url
        $databases = array();
        foreach ($this->configValues['data_dir'] as $key => $path) {
            $sanitizedKey = $stringUtils->removeAccents($key);
            $sanitizedKey = preg_replace('/[^\w]/', '-', $sanitizedKey);
            $sanitizedKey = preg_replace('/-{2,}/', '-', $sanitizedKey);

            $databases[$sanitizedKey] = $path;
        }
        $this->configValues['data_dir'] = $databases;

        $this->configValues['default_database_key'] = key($this->configValues['data_dir']);
    }

    /**
     * Get a configuration value from a key
     *
     * @param string $confKey
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function getValue($confKey)
    {
        if (!array_key_exists($confKey, $this->configValues)) {
            throw new \InvalidArgumentException(sprintf("Config value %s doest not exist", $confKey));
        }

        return $this->configValues[$confKey];
    }

    /**
     * Get database path
     *
     * @param  string $dbKey Database key
     *
     * @return string
     */
    public function getDatabasePath($dbKey = 'default')
    {
        if ($dbKey == 'default') {
            $dbKey = $this->configValues['current_database_key'];
        }

        return $this->preprendBaseDir($this->configValues['data_dir'][$dbKey]);
    }

    /**
     * Get internal database path
     *
     * @return string
     */
    public function getInternalDatabasePath()
    {
        return $this->preprendBaseDir($this->configValues['internal_db']);
    }

    /**
     * Prepend base dir to data path if needed
     *
     * @param string $path
     *
     * @return string
     */
    protected function preprendBaseDir($path)
    {
        // Check if path is relative or absolute
        if (strpos($path, DS) !== 0) {
            $path = BASE_DIR . $path;
        }

        return $path;
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
        $this->configValues[$confKey] = $confValue;
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

     /**
      * Get admin path value
      *
      * @return string
      */
     public function getAdminPath()
     {
         return DS.trim($this->getValue('admin_path'), '/');
     }
}
