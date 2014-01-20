<?php

namespace Cops\Tests\Model;

/**
 * Common model test cases
 *
 * @require PHP 5.2
 */
class CommonTest extends \PHPUnit_Framework_TestCase
{

    private $book;

    private function getModel()
    {
        if (null == $this->book) {
            $this->book = new \Cops\Model\Book;
        }
        return $this->book;
    }

    /**
     * @expectedException Exception
     */
    public function testCallError()
    {
        $this->getModel()->whatever();
    }

    public function testNonExistentPropertyGetter()
    {
        $this->assertEquals($this->getModel()->getDummy(), null);
    }

    public function testSetData()
    {
        $model = $this->getModel();
        $model->setData(array('id' => 1));
        $this->assertEquals($model->getId(), 1);

        $model = new \Cops\Model\Book(array('id' => 1));
        $this->assertEquals($model->getId(), 1);
    }

}
