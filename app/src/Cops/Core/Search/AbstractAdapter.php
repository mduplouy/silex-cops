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

use Cops\Core\Entity\BookCollection;

/**
 * Abstract search adapter
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractAdapter
{
    /**
     * Book collection instance
     * @var BookCollection
     */
    protected $bookCollection;

    /**
     * Constructor
     *
     * @param BookCollection $bookCollection
     */
    public function __construct(BookCollection $bookCollection)
    {
        $this->bookCollection = $bookCollection;
    }
}