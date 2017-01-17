<?php

namespace Cops\Tests\Core\Entity;

use Cops\Tests\AbstractTestCase;

/**
 * Author repository
 */
class AuthorRepositoryTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $instance = $this->getAuthorRepository();

        $this->assertInstanceOf('\Cops\Core\Entity\AuthorRepository', $instance);
        $this->assertInstanceOf('\Cops\Core\Entity\RepositoryInterface\AuthorRepositoryInterface', $instance);
    }

    /**
     * Get author repository
     * @return \Cops\Core\Èntity\AuthorRepository
     */
    protected function getAuthorRepository()
    {
        return $this->app['repository.author'];
    }

}
