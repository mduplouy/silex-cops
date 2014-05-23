<?php

namespace Cops\Tests\Model;

use Cops\Model\Core;
use Silex\WebTestCase;

/**
 * EntityAbstract model test cases
 *
 * @require PHP 5.3
 */
class EntityAbstractTest extends WebTestCase
{
    private $book;

    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    private function getBook()
    {
        if (null == $this->book) {
            $this->book = $this->app['model.book'];
        }
        return $this->book;
    }

    /**
     * @expectedException Exception
     */
    public function testCallError()
    {
        $this->getBook()->whatever();
    }

    public function testNonExistentPropertyGetter()
    {
        $this->assertEquals($this->getBook()->getDummy(), null);
    }

    public function testSetData()
    {
        $model = $this->getBook();
        $model->setData(array('id' => 1));
        $this->assertEquals($model->getId(), 1);

        $model = $this->app['model.book']->setData(array('id' => 1));
        $this->assertEquals($model->getId(), 1);
    }
}
