<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

/**
 * Serie model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Serie extends Common
{
    /**
     * Resource name
     * @var string
     */
    protected $_resourceName = 'Resource\\Serie';

    /**
     * Get aggregated series by first letter
     *
     * @return array
     */
    public function getAggregatedList()
    {
        $series = array();
        foreach($this->getResource()->getAggregatedList() as $serie) {
            // Force non alpha to #
            if (!preg_match('/[A-Z]/', $serie['first_letter'])) {
                $serie['first_letter'] = '#';
            }
            $series[$serie['first_letter']] = $serie['nb_serie'];
        }
        return $series;
    }
}