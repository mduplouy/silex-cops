<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Archive;

/**
 * Archive factory
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface ArchiveInterface
{
    /**
     * Generate archive
     *
     * @return string The path to archive file
     */
    public function generateArchive();

    /**
     * Add files to archive
     *
     * @param \Cops\Model\BookFile\Collection $collection
     *
     * @return \Cops\Model\Archive\ArchiveInterface
     */
    public function addFiles(\Cops\Model\BookFile\Collection $collection);

    /**
     * Get file extension
     *
     * @return string
     */
    public function getExtension();

}