<?php

namespace Cops\Tests\Model;

use Cops\Tests\AbstractTestCase;

/**
 * Cover model test cases
 *
 * @require PHP 5.3
 */
class CoverTest extends AbstractTestCase
{
    public function testThumbnailGeneration()
    {
        $book = new \Cops\Model\Book($this->app);
        $book->load(5);

        $currentDb = $this->app['config']->getValue('current_database_key');

        $thumbnail = $book->getCover()->getThumbnailPath();
        $this->assertRegExp('#/assets/books/'.$currentDb.'/5/*.*/5.jpg#', (string) $thumbnail);

        $targetPath = BASE_DIR.$this->app['config']->getValue('public_dir');

        // cleaning
        unlink($targetPath.$thumbnail);
        rmdir(dirname($targetPath.$thumbnail));
        rmdir($targetPath.DS.'assets'.DS.'books'.DS.$currentDb.DS.'5');

        $cover = $book->getCover();
        $thumbnail = '/assets/books/'.$currentDb.'/5/320x240/5.jpg';
        $this->assertFileNotExists($targetPath.$thumbnail);

        $thumbnail = $cover->getThumbnailPath(320, 240);
        $this->assertRegExp('#'.$thumbnail.'#', $thumbnail);

        $this->assertFileExists($targetPath.$thumbnail);

        $thumbnail = $cover->getThumbnailPath(320, 240);

        // cleaning
        unlink($targetPath.$thumbnail);
        rmdir(dirname($targetPath.$thumbnail));
        rmdir($targetPath.DS.'assets'.DS.'books'.DS.$currentDb.DS.'5');
    }

}
