<?php

namespace Cops\Tests\Model\Search;

use Silex\WebTestCase;

/**
 * Book model test cases
 *
 * @require PHP 5.3
 */
class SearchFactoryTest extends  WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../../application.php';
    }

    /**
     * @expectedException \Cops\Exception\Search\AdapterException
     */
    public function testGetInstanceException()
    {
        $this->app['factory.search']
            ->getInstance('dummy-search');
    }
}
