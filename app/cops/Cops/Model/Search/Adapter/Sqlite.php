<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Search\Adapter;

use Cops\Model\Core;
use Cops\Model\Book;
use Cops\Model\Search\SearchInterface;
use \Doctrine\DBAL\Driver\PDOStatement;

/**
 * Sqlite search adapter class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Sqlite extends Core implements SearchInterface
{

    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function sendRequest()
    {

    }

    public function getResults($searchTerm, $page)
    {
        $nbItems = $this->getConfig()->getValue('page_result');

        $books = $this->book->getCollection()
            ->setFirstResult(($page-1) * $nbItems)
            ->setMaxResults($nbItems)
            ->getByKeyword(explode('-', $searchTerm))
            ->addBookFiles();

        return $books;
    }

}
