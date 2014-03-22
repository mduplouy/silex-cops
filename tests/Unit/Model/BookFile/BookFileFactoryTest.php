<?php

namespace Cops\Tests\Model\BookFile;

use Silex\WebTestCase;

/**
 * BookFile factory test cases
 *
 * @require PHP 5.3
 */
class BookFileFactoryTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../../application.php';
    }

    /**
     * @expectedException \Cops\Exception\BookFile\AdapterException
     */
    public function testBadFormatException()
    {
        $bookFile = $this->app['factory.bookfile']->getInstance('DUMMY');
    }


}
