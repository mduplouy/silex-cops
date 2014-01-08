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

use Cops\Model\Core;
use Cops\Model\Book;

/**
 * Search abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class SearchAbstract extends Core
{
    /**
     * Book collection instance
     *
     * @var Collection
     */
    protected $collection;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collection = $this->getModel('Book')->getCollection();
    }
}