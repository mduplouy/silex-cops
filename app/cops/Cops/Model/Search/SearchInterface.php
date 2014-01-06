<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Search;

/**
 * Search interface
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface SearchInterface
{
    public function sendRequest();


    public function getResults($searchTerm, $page);
}