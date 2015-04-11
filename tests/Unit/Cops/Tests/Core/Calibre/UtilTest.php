<?php

namespace  Cops\Tests\Core\Calibre;

use Cops\Tests\AbstractTestCase;

/**
 * Calibre Util
 *
 * @require PHP 5.3
 */
class UtilTest extends AbstractTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetAuthorSortMethodException()
    {
        $this->app['calibre-util']->setAuthorSortMethod('dummy');
    }

    /**
     * @dataProvider getDataForAuthorSortAlgo
     */
    public function testAuthorSortAlgorithm($sortMethod, $authorName, $expectedSort)
    {
        $authorSort = $this->app['calibre-util']
            ->setAuthorSortMethod($sortMethod)
            ->getAuthorSortName($authorName);

        $this->assertEquals(
            $authorSort,
            $expectedSort,
            sprintf(
                'Calibre "%s" algorithm gives "%s" result instead of "%s"',
                $sortMethod,
                $authorSort,
                $expectedSort
            )
        );
    }

    /**
     * Data provider for author sort testing
     */
    public function getDataForAuthorSortAlgo()
    {
        return array(
            array('invert',  'John Smith',       'Smith, John'),
            array('invert',  'John Steve Smith', 'Smith, John Steve'),
            array('invert',  'Smith',            'Smith'),
            array('comma',   'Smith, John',      'Smith, John'),
            array('comma',   'John Smith',       'Smith, John'),
            array('nocomma', 'John Smith',       'Smith John'),
        );
    }

    /**
     * @dataProvider getDataForTitleSortAlgo
     */
    public function testTitleSortAlgorithm($title, $expected, $lang)
    {
        $titleSort = $this->app['calibre-util']->getTitleSort($title, $lang);

        $this->assertEquals(
            $titleSort,
            $expected,
            sprintf(
                'Calibre title sort gives "%s" result instead of "%s"',
                $titleSort,
                $expected
            )
        );
    }

    /**
     * Data provider for title sort testing
     */
    public function getDataForTitleSortAlgo()
    {
        return array(
            array('Le titre test',  'titre test, Le',  'fr'),
            array('The title test', 'title test, The', 'en'),
            array('The title test', 'title test, The', false),
        );
    }
}
