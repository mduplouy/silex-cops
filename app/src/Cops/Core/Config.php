<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core;

use Cops\Core\StringUtils;
use Cops\Core\Application;
use Cops\Security\Provider\SecurityServiceProvider;

/**
 * Simple configuration class with hardcoded default values and override by ini file
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
        // Language
        'default_lang'            => 'fr',

        // Page size
        'last_added'              => 15,
        'by_date_page_size'       => 25,
        'author_page_size'        => 25,
        'tag_page_size'           => 25,
        'serie_page_size'         => 25,
        'homepage_tags'           => 30,
        'book_per_page'           => 30,

        // Paths
        'theme'                   => 'default',
        'mobile_theme'            => 'mobile',
        'public_dir'              => 'web',
        'data_dir'                => array('default' => 'data'),
        'internal_db'             => 'data/silexCops',

        // Email
        'sender'                  => 'php',

        // cover
        'image_processor'         => 'gd',
        'cover_width'             => 160,
        'cover_height'            => 260,

        // admin
        'admin_path'              => '/admin',
        'default_login'           => 'admin',
        'default_password'        => 'password',

        // Auth & rights
        'use_auth'                => true,
        'auth_method'             => 'http',
        'inline_edit_enable'      => true,
        'user_actions_enable'     => true,

        // User actions
        'user_actions_page_size'  => 20,

        // Misc.
        'debug'                   => false,
        'use_rewrite'             => false,

        // Search
        'search_engine'           => 'sqlite',
        'search_page_size'        => 25,

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
     * String utils instance
     * @var StringUtils
     */
    protected $utils;

    /**
     * Constructor
     *
     * @param string       $configFilePath
     * @param StringUtils  $stringUtils
     * @param array        $override
     */
    public function __construct($configFilePath, StringUtils $stringUtils, array $override = array())
    {
        $this->utils = $stringUtils;

        $this->readParams($configFilePath, $override);

        if (!is_array($this->configValues['data_dir'])) {
            $this->configValues['data_dir'] = array('default' => $this->configValues['data_dir']);
        }

        $this->initDatabases();
    }

    /**
     * Read params and handle override
     *
     * @param string $configFilePath
     * @param array  $override
     *
     * @return void
     */
    protected function readParams($configFilePath, array $override)
    {
        if (file_exists($configFilePath)) {
            $confValues = (array) parse_ini_file($configFilePath, false);
            $this->configValues = array_merge($this->configValues, $confValues);
        }

        $this->configValues = array_merge($this->configValues, $override);
    }

    /**
     * Init database keys / path
     *
     * @return void
     */
    protected function initDatabases()
    {
        // Sanitize db key to use it in url
        $databases = array();
        foreach ($this->configValues['data_dir'] as $key => $path) {
            $databases[$this->utils->urlSafe($key)] = $path;
        }

        $this->configValues['data_dir'] = $databases;

        $this->configValues['default_database_key'] = key($this->configValues['data_dir']);
        $this->configValues['current_database_key'] = $this->configValues['default_database_key'];
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
    public function getDatabasePath($dbKey = null)
    {
        if ($dbKey === null) {
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
        if (strpos($path, DIRECTORY_SEPARATOR) !== 0) {
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
     * Set data dir
     *
     * @param  mixed $dataDir
     *
     * @return $this
     */
    public function setDataDir($dataDir)
    {
        if (!is_array($dataDir)) {
            $dataDir = array('default' => $dataDir);
        }

        $this->configValues['data_dir'] = $dataDir;

        $this->initDatabases();

        return $this;
    }

    /**
     * Set database key in use
     *
     * @param Application $app
     * @param string      $dbKey
     *
     * @return Config
     *
     * @throws \InvalidArgumentException
     */
    public function setDatabaseKey(Application $app, $dbKey)
    {
        if ($dbKey === null) {
            $dbKey = $this->getValue('default_database_key');
        }

        if (!array_key_exists($dbKey, $this->getValue('data_dir'))) {
            throw new \InvalidArgumentException('Database does not exist');
        }

        $app['db'] = $app->share($app->extend('db', function($db, $app) use($dbKey) {
            return $app['dbs'][$dbKey];
        }));

        $this->setValue('current_database_key', $dbKey);
        $this->setValue('current_database_path', $this->getDatabasePath($dbKey));

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

    /**
     * Display logout link ?
     *
     * @return bool
     */
    public function displayLogoutLink()
    {
        return $this->configValues['use_auth'] &&
            $this->configValues['auth_method'] == SecurityServiceProvider::AUTH_METHOD_FORM;
    }
}
