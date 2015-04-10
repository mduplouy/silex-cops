<?php

namespace Cops\Tests\Core;

use Cops\Tests\AbstractTestCase;

/**
 * Cover
 *
 * @require PHP 5.3
 */
class CoverTest extends AbstractTestCase
{
    public function testServiceInstance()
    {
        $this->assertInstanceOf('\Cops\Core\Cover', $this->getCover());
    }

    public function testGetThumbnailPath()
    {
        $cover = $this->getCover();
        $this->assertFalse($cover->getThumbnailPath());
    }

    /**
     * Get cover
     * @return \Cops\Core\Cover
     */
    protected function getCover()
    {
        return $this->app['cover'];
    }
}
