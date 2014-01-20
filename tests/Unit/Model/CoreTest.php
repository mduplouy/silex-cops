<?php

namespace Cops\Tests\Model;

use Cops\Model\Core;

/**
 * Core model test cases
 *
 * @require PHP 5.2
 */
class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testGetModel()
    {
        $tag = Core::getModel('Tag');
        $this->assertInstanceOf('Cops\Model\Tag', $tag);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetModelException()
    {
        Core::getModel('dummy');
    }

    public function testConfigInstance()
    {
        $this->assertInstanceOf('Cops\Model\Config', Core::getConfig());
    }
}
