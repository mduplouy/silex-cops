<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Search;

/**
 * Search adapter interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface AdapterInterface
{
    /**
     * Get a book collection matching search results
     *
     * @param array $searchTerms
     * @param int   $page
     *
     * @return \Cops\Core\Entity\BookCollection
     */
    public function getResults(array $searchTerms, $page);
}
