<?php

namespace Cops\Tests\Model;

use Silex\WebTestCase;

/**
 * Calibre model test cases
 *
 * @require PHP 5.3
 */
class CalibreTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    /**
     * @dataProvider getDataForAuthorSortAlgo
     */
    public function testAuthorSortAlgorithm($sortMethod, $authorName, $expectedSort)
    {
        $this->app['config']->setValue('author_sort_copy_method', $sortMethod);

        $authorSort = $this->app['model.calibre']->getAuthorSortName($authorName);

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
        $titleSort = $this->app['model.calibre']->getTitleSort($title, $lang);

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

