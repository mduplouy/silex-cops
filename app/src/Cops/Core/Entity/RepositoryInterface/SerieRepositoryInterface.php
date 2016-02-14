<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\RepositoryInterface;

use Cops\Core\RepositoryInterface;

/**
 * Serie repository interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface SerieRepositoryInterface extends RepositoryInterface
{
    /**
     * Find by id
     *
     * @param  int   $serieId
     *
     * @return array
     */
    public function findById($serieId);

    /**
     * Count book number in serie
     *
     * @param  int $serieId
     *
     * @return int
     */
    public function countBooks($serieId);

    /**
     * Count series by first letter
     *
     * @return array
     */
    public function countGroupedByFirstLetter();

    /**
     * Count series
     *
     * @return int
     */
    public function countAll();

    /**
     * Find by first letter
     *
     * @param string  $letter
     *
     * @return array
     */
    public function findByFirstLetter($letter);
}
