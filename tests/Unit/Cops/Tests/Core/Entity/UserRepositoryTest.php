<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

class UserRepositoryTest extends AbstractTestCase
{
    public function testInitTable()
    {
        $internalDb = $this->app['config']->getValue('internal_db');

        unlink($internalDb);
        $this->assertFileNotExists($internalDb);

        $this->app['repository.user']->initTable();
        $this->assertFileExists($internalDb);

        unlink($internalDb);
    }
}
