<?php

namespace Cops\Tests\Model;

use Silex\WebTestCase;

/**
 * Config test cases
 *
 * @require PHP 5.3
 */
class ConfigTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    /**
     * @dataProvider valueProvider
     */
    public function testGetValue($value)
    {
        $returnType = $this->app['config']->setValue('last_added', $value);
        $this->assertInstanceOf('Cops\Model\Config', $returnType);
        $this->assertEquals($this->app['config']->getValue('last_added'), $value);
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
        $config = $this->app['config']->getValue('dummy_value');
    }

    /**
     * @dataProvider templatePrefixProvider
     */
    public function testSetTemplatePrefix($prefix, $expectedValue)
    {
        $returnType = $this->app['config']->setTemplatePrefix($prefix);

        $this->assertInstanceOf('Cops\Model\Config', $returnType);
        $this->assertEquals($expectedValue, $this->app['config']->getTemplatePrefix());
    }

    public function templatePrefixProvider()
    {
        return array(
            array(DS.'toto', DS.'toto'.DS),
            array('tata', 'tata'.DS),
        );
    }
}