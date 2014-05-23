<?php

namespace Cops\Tests\Model;

use Silex\WebTestCase;


/**
 * BookFile model test cases
 *
 * @require PHP 5.3
 */
class BookFileTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    public function testCloneResetProperties()
    {
        $bookFile = $this->app['model.bookfile'];

        $bookFile->setFormat('PDF')
            ->setUncompressedSize('100')
            ->setName('dummy')
            ->setDirectory('/tmp');

        $clone = clone $bookFile;

        $this->assertTrue($clone->getFormat() === null,      "BookFile format not null after cloning");
        $this->assertTrue($clone->getUncompressedSize() === 0, "BookFile size not empty after cloning");
        $this->assertTrue($clone->getName() === null,  "BookFile name not null after cloning");
        $this->assertTrue($clone->getDirectory() === null, "BookFile directory not null after cloning");
    }

    public function testPopulateBookCollection()
    {
        $bookFile = $this->app['model.bookfile'];

        $collection = $this->app['model.book']->getCollection();

        $this->assertInstanceOf(
            '\Cops\Model\Book\Collection',
            $bookFile->populateBookCollection($collection),
            'BookFile::populateBookCollection() does not return Collection object'
        );
    }
}
