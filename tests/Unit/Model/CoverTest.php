<?php

namespace Cops\Tests\Model;

use org\bovigo\vfs\vfsStream;
use Cops\Model\Core;

/**
 * Cover model test cases
 *
 * @require PHP 5.2
 */
class CoverTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sourcePath = __DIR__.'/../../';
        $this->targetPath = BASE_DIR.Core::getConfig()->getValue('public_dir');
    }


    public function testThumbnailWithoutFileIsFalse()
    {
        $book = new \Cops\Model\Book;
        $book->load(5);

        $cover = $book->getCover($this->sourcePath);

        $thumbnail = $cover->getThumbnailPath();

        $this->assertRegExp('#/assets/books/5/*.*/5.jpg#', $thumbnail);

        // cleaning
        unlink($this->targetPath.$thumbnail);
        rmdir(dirname($this->targetPath.$thumbnail));
        rmdir($this->targetPath.DS.'assets'.DS.'books'.DS.'5');

        $thumbnail = $cover->getThumbnailPath(320, 240);
        $this->assertRegExp('#/assets/books/5/320x240/5.jpg#', $thumbnail);

    }

}
