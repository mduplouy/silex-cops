<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\BookFile;

use Cops\Model\FactoryAbstract;
use Silex\Application as BaseApplication;
use Cops\Exception\BookFile\AdapterException;


/**
 * Book file factory
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookFileFactory extends FactoryAbstract
{
    /**
     * File types
     */
    const TYPE_EPUB = 'EPUB';
    const TYPE_PDF  = 'PDF';
    const TYPE_MOBI = 'MOBI';

    /**
     * File types storage
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

        $this->instanceTypeStorage[self::TYPE_EPUB] = self::TYPE_EPUB;
        $this->instanceTypeStorage[self::TYPE_PDF]  = self::TYPE_PDF;
        $this->instanceTypeStorage[self::TYPE_MOBI] = self::TYPE_MOBI;
    }

    /**
     * Instance getter
     *
     * @return \Cops\Model\BookFile\BookFileInterface
     */
    public function getInstance($instance = self::TYPE_EPUB)
    {
        if (!isset($this->instanceTypeStorage[$instance])) {
            throw new AdapterException(
                sprintf(
                    'No model configured for the %s book file format',
                    $this->$instance
                )
            );
        }

        $className = __NAMESPACE__.'\\Adapter\\' . ucfirst(strtolower($instance));
        return new $className($this->app);
    }

    /**
     * File types getter
     *
     * @return array
     */
    public function getFileTypes()
    {
        return $this->_instanceTypeStorage;
    }
}