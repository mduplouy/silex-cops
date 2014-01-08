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

use Cops\Model\Search\SearchAbstract;
use Cops\Model\Search\SearchInterface;

/**
 * Sqlite search adapter class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Sqlite extends SearchAbstract implements SearchInterface
{
    /**
     * Get a book collection matching search results
     *
     * @param array $searchTerm
     * @param int   $page
     *
     * @return Collection
     */
    public function getResults(array $searchTerms, $page)
    {
        $nbItems = $this->getConfig()->getValue('page_result');

        return $this->collection
            ->setFirstResult(($page-1) * $nbItems)
            ->setMaxResults($nbItems)
            ->getByKeyword($searchTerms)
            ->addBookFiles();
    }
}
