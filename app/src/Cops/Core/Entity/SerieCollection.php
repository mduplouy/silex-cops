<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity;

use Cops\Core\AbstractCollection;

/**
 * Serie collection
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class SerieCollection extends AbstractCollection
{
    /**
     * Get aggregated series by first letter
     *
     * @return array
     */
    public function countGroupedByFirstLetter()
    {
        $series = array();
        foreach ($this->getRepository()->countGroupedByFirstLetter() as $serie) {
            // Force non alpha to #
            if (!preg_match('/[A-Z]/', $serie['first_letter'])) {
                $serie['first_letter'] = '#';
            }
            if (!array_key_exists($serie['first_letter'], $series)) {
                $series[$serie['first_letter']] = 0;
            }
            $series[$serie['first_letter']] += $serie['nb_serie'];
        }

        return $series;
    }

    /**
     * Get collection based on first letter
     *
     * @param  str   $letter
     *
     * @return $this
     */
    public function findByFirstLetter($letter)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByFirstLetter($letter)
        );
    }

    /**
     * Count all series
     *
     * @return int
     */
    public function countAll()
    {
        return $this->getRepository()->countAll();
    }
}
