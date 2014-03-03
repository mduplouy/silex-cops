<?php

namespace Cops\Tests\Model;

/**
 * Common model test cases
 *
 * @require PHP 5.3
 */
class CommonTest extends \PHPUnit_Framework_TestCase
{

    private $book;

    private function getBook()
    {
        if (null == $this->book) {
            $this->book = new \Cops\Model\Book(\Cops\Model\Core::getApp());
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

        $model = new \Cops\Model\Book(\Cops\Model\Core::getApp(), array('id' => 1));
        $this->assertEquals($model->getId(), 1);
    }

}
