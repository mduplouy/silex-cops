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

use Cops\Model\ResourceAbstract;
use Cops\Exception\SerieException;
use Cops\Model\Core;
use PDO;
use Doctrine\DBAL\Connection;


/**
 * Serie resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends ResourceAbstract
{
    /**
     * Load a serie data
     *
     * @param  int               $serieId
     *
     * @throws SerieException
     *
     * @return array
     */
    public function load($serieId)
    {
        $result = $this->getBaseSelect()
            ->select('main.*')
            ->from('series', 'main')
            ->where('id = :serie_id')
            ->setParameter('serie_id', $serieId, PDO::PARAM_INT)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            throw new SerieException(sprintf(
                'Serie width id %s not found',
                $serieId
            ));
        }

        return $result;
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
        return $this->getBaseSelect()
            ->select('COUNT(*)')
            ->from('series', 'main')
            ->innerJoin('main', 'books_series_link', 'bsl',   'bsl.series = main.id')
            ->innerJoin('main', 'books',             'books', 'bsl.book = books.id')
            ->where('main.id = :serie_id')
            ->setParameter('serie_id', $serieId, PDO::PARAM_INT)
            ->execute()
            ->fetchColumn();
    }

    /**
     * Get aggregated series by first letter
     *
     * @return array
     */
    public function getAggregatedList()
    {
        return $this->getBaseSelect()
            ->select(
                'DISTINCT UPPER(SUBSTR(sort, 1, 1)) AS first_letter',
                'COUNT(*) AS nb_serie'
            )
            ->from('series', 'series')
            ->groupBy('first_letter')
            ->orderBy('first_letter')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve data based on first letter
     *
     * @param string  $letter
     *
     * @return array
     */
    public function loadByFirstLetter($letter)
    {
        $qb = $this->getBaseSelect()
            ->select('main.*', 'COUNT(bsl.series) AS book_count')
            ->from('series', 'main')
            ->innerJoin('main', 'books_series_link', 'bsl', 'bsl.series = main.id')
            ->where('main.id = :serie_id');

        if ($letter !== '#') {
            $qb->where('UPPER(SUBSTR(sort, 1, 1)) = ?')
                ->setParameter(1, $letter, PDO::PARAM_STR);
        } else {
            $qb->where('UPPER(SUBSTR(sort, 1, 1)) NOT IN (:letters)')
                ->setParameter('letters', Core::getLetters(), Connection::PARAM_STR_ARRAY);
        }

        return $qb->groupBy('main.id')
            ->orderBy('sort')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

}