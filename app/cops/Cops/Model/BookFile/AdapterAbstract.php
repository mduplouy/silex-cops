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

use Cops\Model\EntityAbstract;
use Silex\Application as BaseApplication;

/**
 * Book file abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AdapterAbstract extends EntityAbstract
{
    /**
     * Application instance
     * @var \Silex\Application
     */
    protected $app;

    /**
     * Bookfile ID
     * @var int
     */
    protected $id;

    /**
     * Book ID
     * @var int
     */
    protected $bookId;

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
     * Constructor
     *
     * @param array $dataArray
     */
    public function __construct(BaseApplication $app, array $dataArray = array())
    {
        $this->app       = $app;
        $this->directory = $app['book_storage_dir'];
        $this->setData($dataArray);
    }

    /**
     * Get the file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->app['book_storage_dir'] . DS
            . $this->directory . DS
            . $this->name . '.'
            . strtolower($this->format);
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
        $size = $this->uncompressedSize;
        $label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
        $labelCount = count($label);
        for ($i = 0; $size >= 1024 && $i < ($labelCount -1); $size /= 1024, $i++);
        return round($size, $i-1) . ' ' . $this->app['translator']->trans($label[$i]);
    }

}
