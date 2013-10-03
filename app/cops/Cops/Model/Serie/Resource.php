<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Serie;

use Cops\Model\Exception\SerieException;
use Cops\Model\Core;
use \PDO;

/**
 * Serie resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends \Cops\Model\Resource
{
    protected $_baseSelect = 'SELECT
        main.*
        FROM series AS main';

    /**
     * Load a serie data
     *
     * @param  int               $serieId
     * @param  \Cops\Model\Serie $serie
     *
     * @return \Cops\Model\Serie;
     */
    public function load($serieId, \Cops\Model\Serie $serie)
    {
        $result = $this->getConnection()
            ->fetchAssoc(
                $this->getBaseSelect(). ' WHERE id = ?',
                array(
                    (int) $serieId,
                )
            );

        if (empty($result)) {
            throw new SerieException(sprintf(
                'Serie width id %s not found',
                $serieId
            ));
        }

        return $serie->setData($result);
    }

    /**
     * Count book number in serie
     *
     * @param  int $serieId
     *
     * @return int
     */
    public function countBooks($serieId)
    {
        $sql = 'SELECT
            COUNT(*) FROM series
            INNER JOIN books_series_link ON series.id = books_series_link.series
            INNER JOIN books ON books_series_link.book = books.id
            WHERE series.id = ?';

        return (int) $this->getConnection()
            ->fetchColumn(
                $sql,
                array(
                    (int) $serieId,
                ),
                0
            );
    }

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