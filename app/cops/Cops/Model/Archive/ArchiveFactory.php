<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Archive;

use Cops\Model\FactoryAbstract;
use Silex\Application as BaseApplication;
use Cops\Exception\Archive\AdapterException;

/**
 * Archive factory
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class ArchiveFactory extends FactoryAbstract
{
    /**
     * Archive type keys
     */
    const TYPEKEY_ZIP   = 'zip';
    const TYPEKEY_TARGZ = 'tar.gz';

    /**
     * Archive types
     */
    const TYPE_ZIP    = 'Zip';
    const TYPE_TARGZ  = 'TarGz';

    /**
     * Archive types storage
     * @var array
     */
    protected $instanceTypeStorage = array();

    /**
     * Constructor
     *
     * @param \Silex\Application $app
     */
    public function __construct(BaseApplication $app)
    {
        parent::__construct($app);

        $this->instanceTypeStorage[self::TYPEKEY_ZIP] = self::TYPE_ZIP;
        $this->instanceTypeStorage[self::TYPEKEY_TARGZ]  = self::TYPE_TARGZ;
    }

    /**
     * Instance getter
     *
     * @return \Cops\Model\Image\ImageProcessorInterface
     */
    public function getInstance($instance = self::TYPEKEY_TARGZ)
    {
        if (!isset($this->instanceTypeStorage[$instance])) {
            throw new AdapterException(
                sprintf('No model configured for the %s archive format', $instance)
            );
        }
        $className = __NAMESPACE__ . '\\Adapter\\' . $this->instanceTypeStorage[$instance];
        return new $className($this->app);
    }
}