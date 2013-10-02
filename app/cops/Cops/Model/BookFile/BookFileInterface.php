<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\BookFile;

/**
 * Book file interface
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface BookFileInterface
{
    /**
     * Get the file path
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Get file name with extension
     *
     * @return string
     */
    public function getFileName();

    /**
     * Get content type header for download
     *
     * @return string
     */
    public function getContentTypeHeader();

    /**
     * Get human readable file size
     *
     * @return string
     */
    public function getFormattedSize();
}
