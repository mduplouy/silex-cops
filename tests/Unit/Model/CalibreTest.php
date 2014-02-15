<?php

namespace Cops\Tests\Model;

/**
 * Calibre model test cases
 *
 * @require PHP 5.3
 */
class CalibreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getDataForSortAlgo
     */
    public function testAuthorSortAlgorithm($sortMethod, $authorName, $expectedSort)
    {
        $config = \Cops\Model\Core::getConfig();
        $config->setValue('author_sort_copy_method', $sortMethod);

        $calibre = new \Cops\Model\Calibre(\Cops\Model\Core::getApp());

        $authorSort = $calibre->getAuthorSortName($authorName);

        $this->assertEquals(
            $authorSort,
            $expectedSort,
            sprintf(
                'Calibre %s algorithm gives %s result instead of %s',
                $sortMethod,
                $authorSort,
                $expectedSort
            )
        );
    }

    /**
     * Data provider for author sort testing
     */
    public function getDataForSortAlgo()
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

}

