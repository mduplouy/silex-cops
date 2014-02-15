<?php

namespace Cops\Tests\Model;

use Cops\Model\Core;

/**
 * Config model test cases
 *
 * @require PHP 5.3
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $config = new \Cops\Model\Config(BASE_DIR.'app/cops/config.ini');

        $this->assertAttributeInternalType('array', '_configValues', $config);
    }

    /**
     * @dataProvider valueProvider
     */
    public function testGetValue($value)
    {
        $config = Core::getConfig();
        $returnType = $config->setValue('last_added', $value);

        $this->assertInstanceOf('Cops\Model\Config', $returnType);
        $this->assertEquals($config->getValue('last_added'), $value);
    }

    public function valueProvider()
    {
        return array(
            array(15),
            array(30),
            array(45),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetValueKo()
    {
        $config = Core::getConfig()->getValue('dummy_value');
    }

    /**
     * @dataProvider templatePrefixProvider
     */
    public function testSetTemplatePrefix($prefix, $expectedValue)
    {
        $config = Core::getConfig();

        $returnType = $config->setTemplatePrefix($prefix);

        $this->assertInstanceOf('Cops\Model\Config', $returnType);
        $this->assertEquals($expectedValue, $config->getTemplatePrefix());
    }

    public function templatePrefixProvider()
    {
        return array(
            array(DS.'toto', DS.'toto'.DS),
            array('tata', 'tata'.DS),
        );
    }
}