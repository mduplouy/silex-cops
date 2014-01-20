<?php

namespace Cops\Tests\Model;

use Cops\Model\Core;

/**
 * Config model test cases
 *
 * @require PHP 5.2
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $config = new \Cops\Model\Config(BASE_DIR.'app/cops/config.ini');

        $this->assertAttributeInternalType('array', '_configValues', $config);
    }

    public function testGetValue()
    {
        $config = Core::getConfig()->getValue('last_added');
        $this->assertEquals($config, 15);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetValueKo()
    {
        $config = Core::getConfig()->getValue('dummy_value');
    }
}