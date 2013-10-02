<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

use Cops\Model\Core;

/**
 * Archive model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class ArchiveAbstract extends Core
{
    /**
     * Files to add to archive
     * @var array
     */
    protected $_files = array();

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Add files to current archive
     *
     * @param \Cops\Model\BookFile\Collection $collection
     *
     * @return \Cops\Model\ArchiveInterface
     */
    public function addFiles(\Cops\Model\BookFile\Collection $collection)
    {
        $this->_files = $collection;
        return $this;
    }
}