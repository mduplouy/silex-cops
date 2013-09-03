<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Resource;

/**
 * Serie resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Serie extends \Cops\Model\Resource
{
    /**
     * Get aggregated series by first letter
     *
     * @return array
     */
    public function getAggregatedList()
    {
        $db = $this->getConnection();

        $sql = 'SELECT
            DISTINCT UPPER(SUBSTR(sort, 1, 1)) AS first_letter,
            COUNT(*) AS nb_serie
            FROM series
            GROUP BY first_letter
            ORDER BY first_letter';

        return $db->fetchAll($sql);
    }
}