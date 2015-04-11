<?php

namespace Cops\Tests\Command\Provider;

use Cops\Tests\AbstractTestCase;

class CommandServiceProviderTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->app->boot($this->app);

        $cacheWarmup = $this->app['command.cache-warmup'];
        $this->assertInstanceOf('\Cops\Command\GenerateThumbnails', $cacheWarmup);

        $initDb = $this->app['command.init-database'];
        $this->assertInstanceOf('\Cops\Command\InitDatabase', $initDb);
    }
}
