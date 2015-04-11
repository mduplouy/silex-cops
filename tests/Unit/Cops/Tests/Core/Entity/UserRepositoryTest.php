<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

class UserRepositoryTest extends AbstractTestCase
{
    public function testInitTable()
    {
        $internalDb = $this->app['config']->getValue('internal_db');

        $this->app['repository.user']->getConnection()->getSchemaManager()->dropDatabase($internalDb);

        $this->assertFileNotExists($internalDb);

        $this->app['repository.user']->getConnection()->getSchemaManager()->createDatabase($internalDb);
        $this->app['repository.user']->createTable();
        $this->assertFileExists($internalDb);
    }
}
