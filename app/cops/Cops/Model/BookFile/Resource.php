<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\BookFile;

use Cops\Model\Exception\BookFileException;
use Cops\Model\Core;
use \PDO;

/**
 * BookFile resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends \Cops\Model\Resource
{
    /**
     * Get book files by serie ID
     *
     * @param int                  $serieId
     * @param \Cops\Model\BookFile $bookFile
     *
     * @return \Cops\Model\BookFile\Collection
     */
    public function getCollectionBySerieId($serieId, $bookFile)
    {
        $sql = 'SELECT
            data.format,
            data.uncompressed_size,
            data.name,
            books.path as directory
            FROM books_series_link
            INNER JOIN data ON
                data.book = books_series_link.book
            INNER JOIN books ON
                books.id = data.book
            WHERE books_series_link.series = :serie_id';

        $stmt = $this->getConnection()
            ->prepare($sql);

        $stmt->bindValue(':serie_id', $serieId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $collection = $bookFile->getCollection();

        foreach($stmt as $result) {
            $myBookFile = clone($bookFile);

            $myBookFile->setData($result);
            $collection->add($myBookFile);
        }

        return $collection;
    }
}
