<?php

namespace Cops\Tests\Core;

use Cops\Tests\AbstractTestCase;

/**
 * Abstract factory test
 *
 * @require PHP 5.3
 */
class AbstractFactoryTest extends AbstractTestCase
{
    /**
     * @expectedException \UnexpectedValueException
     */
    public function testConstructorException()
    {
        $factory = new \TestAbstractFactory\ConcreteFactory(array('test'));
    }

    /**
     * @expectedException \Cops\Core\AdapterNotFoundException
     */
    public function testGetInstanceException()
    {
        $factory = new \TestAbstractFactory\ConcreteFactory(array(
            'test' => function () {
                return new \stdClass;
            }
        ));
        $factory->getInstance('dummy');
    }

    public function testGetInstance()
    {
        $factory = new \TestAbstractFactory\ConcreteFactory(array(
            'test' => function () {
                return new \stdClass;
            }
        ));

        $this->assertInstanceOf('\stdClass', $factory->getInstance('test'));
    }
}

namespace TestAbstractFactory;

class ConcreteFactory extends \Cops\Core\AbstractFactory
{
}
