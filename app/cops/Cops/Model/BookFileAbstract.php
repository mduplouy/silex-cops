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
use Cops\Model\Common;

/**
 * Book file abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class BookFileAbstract extends Common
{
    /**
     * Bookfile ID
     * @var int
     */
    protected $id;

    /**
     * Bookfile format
     *
     * @var string
     */
    protected $format;

    /**
     * File size in bytes
     *
     * @var int
     */
    protected $uncompressedSize = 0;

    /**
     * File name without extension
     *
     * @var string
     */
    protected $name;

    /**
     * File storage directory
     *
     * @var string
     */
    protected $directory;

    /**
     * Get the file path
     *
     * @return string
     */
    public function getFilePath()
    {
        $filePath = BASE_DIR
            . Core::getConfig()->getValue('data_dir') . DS
            . $this->directory . DS
            . $this->name . '.'
            . strtolower($this->format);

        if (file_exists($filePath)) {
            return $filePath;
        }
    }

    /**
     * Get file name with extension
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->name.'.'.strtolower($this->format);
    }

    /**
     * Get translated human readable file size
     *
     * @return string
     */
    public function getFormattedSize()
    {
        $app = Core::getApp();

        $size = $this->uncompressedSize;
        $label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
        for ($i = 0; $size >= 1024 && $i < ( count( $label ) -1 ); $size /= 1024, $i++);
        return round( $size, $i-1 ) . ' ' . $app['translator']->trans($label[$i]);
    }

}
