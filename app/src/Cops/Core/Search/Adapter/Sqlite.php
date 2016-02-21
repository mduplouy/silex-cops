<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Search\Adapter;

use Cops\Core\Search\AbstractAdapter;
use Cops\Core\Search\AdapterInterface;

/**
 * Sqlite search adapter class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Sqlite extends AbstractAdapter implements AdapterInterface
{
    /**
     * Get a book collection matching search results
     *
     * @param array $searchTerms
     * @param int   $nbItems
     * @param int   $page
     *
     * @return \Cops\Core\Entity\BookCollection
     */
    public function getResults(array $searchTerms, $nbItems = 25, $page = 1)
    {
        $this->bookCollection->setFirstResult(($page-1) * $nbItems);
        if ($nbItems > 0) {
            $this->bookCollection->setMaxResults($nbItems);
        }

        return $this->bookCollection->findByKeyword($searchTerms);
    }
}
