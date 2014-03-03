<?php

namespace Cops\Tests\Model;

use org\bovigo\vfs\vfsStream;
use Cops\Model\Core;

/**
 * Cover model test cases
 *
 * @require PHP 5.3
 */
class CoverTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = \Cops\Model\Core::getApp();
        $this->oldConfigValue = $this->app['config']->getValue('data_dir');
        $this->app['config']->setValue('data_dir', 'tests/data');

        $this->targetPath = BASE_DIR.$this->app['config']->getValue('public_dir');
    }

    public function tearDown()
    {
        $this->app['config']->setValue('data_dir', $this->oldConfigValue);
    }

    public function testThumbnailGeneration()
    {
        $book = new \Cops\Model\Book($this->app);
        $book->load(5);

        $thumbnail = $book->getCover()->getThumbnailPath();
        $this->assertRegExp('#/assets/books/5/*.*/5.jpg#', (string) $thumbnail);

        // cleaning
        unlink($this->targetPath.$thumbnail);
        rmdir(dirname($this->targetPath.$thumbnail));
        rmdir($this->targetPath.DS.'assets'.DS.'books'.DS.'5');

        $cover = $book->getCover();
        $thumbnail = '/assets/books/5/320x240/5.jpg';
        $this->assertFileNotExists($this->targetPath.$thumbnail);

        $thumbnail = $cover->getThumbnailPath(320, 240);
        $this->assertRegExp('#'.$thumbnail.'#', $thumbnail);

        $this->assertFileExists($this->targetPath.$thumbnail);

        $thumbnail = $cover->getThumbnailPath(320, 240);

        // cleaning
        unlink($this->targetPath.$thumbnail);
        rmdir(dirname($this->targetPath.$thumbnail));
        rmdir($this->targetPath.DS.'assets'.DS.'books'.DS.'5');
    }

}
