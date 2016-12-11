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
use Cops\Core\Entity\Serie;
use Cops\Core\Entity\Book;
use Cops\Core\Application;


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
    public function findByFirstLetter($letter, Application $app);

    /**
     * Insert new serie into database
     *
     * @param  Serie $serie
     *
     * @return int Inserted ID
     */
    public function insert(Serie $serie);

    /**
     * Associate serie to book
     *
     * @param  Serie $serie
     * @param  Book  $book
     *
     * @return int    Updated or inserted relation ID
     */
    public function associateToBook(Serie $serie, Book $book);
}
