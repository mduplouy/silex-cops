<?php

namespace Cops\Tests\Core;

use Cops\Tests\AbstractTestCase;

/**
 * Config
 *
 * @require PHP 5.3
 */
class ConfigTest extends AbstractTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetValueException()
    {
        $this->app['config']->getValue('non-existent');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetDatabaseKeyException()
    {
        $this->app['config']->setDatabaseKey($this->app, 'non-existent');
    }
}
