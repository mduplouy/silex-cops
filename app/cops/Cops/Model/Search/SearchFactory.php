<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Search;

use Cops\Model\FactoryAbstract;
use Silex\Application as BaseApplication;
use Cops\Exception\Search\AdapterException;

/**
 * Search factory
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SearchFactory extends FactoryAbstract
{
    /**
     * Instance types
     */
    const TYPE_SQLITE = 'sqlite';

    /**
     * Instance type storage
     * @var array
     */
    private $instanceTypeStorage;

    /**
     * Constructor
     *
     * @param \Silex\Application $app
     */
    public function __construct(BaseApplication $app)
    {
        parent::__construct($app);

        $this->instanceTypeStorage = array(
            self::TYPE_SQLITE => self::TYPE_SQLITE,
        );
    }

    /**
     * Instance getter
     *
     * @return \Cops\Model\Image\SearchInterface
     *
     * @throws \Cops\Exception\Search\AdapterException
     */
    public function getInstance($instance = self::TYPE_SQLITE)
    {
        if (!isset($this->instanceTypeStorage[$instance])) {
            throw new AdapterException(
                sprintf('No model configured for the %s search engine', $instance)
            );
        }

        $className = __NAMESPACE__.'\\Adapter\\' . ucfirst(strtolower($instance));
        return new $className($this->app);
    }
}