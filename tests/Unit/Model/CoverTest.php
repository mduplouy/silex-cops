<?php

namespace Cops\Tests\Model;

/**
 * Cover model test cases
 *
 * @require PHP 5.2
 */
class CoverTest extends \PHPUnit_Framework_TestCase
{

    public function testThumbnailWithoutFileIsFalse()
    {
        $book = new \Cops\Model\Book;

        $cover = $book->getCover();

        $this->assertFalse($cover->getThumbnailPath());
    }



}
