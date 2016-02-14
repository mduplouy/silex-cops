<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Archive;

use Cops\Core\Entity\BookFile\BookFileCollection;
use Cops\Core\Entity\BookFile\AdapterInterface as BookFileAdapterInterface;
use Cops\Core\Entity\BookCollection;

/**
 * Archive adapter interface
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface AdapterInterface
{
    /**
     * Generate archive
     *
     * @return string The path to archive file
     */
    public function generateArchive();

    /**
     * Set current archive files
     *
     * @param BookFileCollection $bookfiles
     *
     * @return self
     */
    public function setFiles(BookFileCollection $bookfiles);

    /**
     * Add file to current archive
     *
     * @param BookFileAdapterInterface $bookfile
     *
     * @return self
     */
    public function addFile(BookFileAdapterInterface $bookfile);

    /**
     * Add files to archive
     *
     * @param  BookCollection $bookfiles
     *
     * @return self
     */
    public function addFiles(BookFileCollection $bookfiles);

    /**
     * Get file extension
     *
     * @return string
     */
    public function getExtension();
}
