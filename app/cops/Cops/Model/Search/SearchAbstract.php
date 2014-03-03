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

use Silex\Application as BaseApplication;

/**
 * Search abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class SearchAbstract
{
    /**
     * Application instance
     * @var \Silex\Application
     */
    protected $app;

    /**
     * Book collection instance
     * @var Collection
     */
    protected $collection;

    /**
     * Constructor
     *
     * @param \Silex\Application $app
     */
    public function __construct(BaseApplication $app)
    {
        $this->app = $app;
        $this->collection = $this->app['model.book']->getCollection();
    }
}