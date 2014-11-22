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

use Cops\Model\BookFile as BaseBookFile;

/**
 * Book file abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AdapterAbstract extends BaseBookFile
{
    /**
     * Get the file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->app['config']->getDatabasePath() . DS
            . $this->directory . DS
            . $this->name . '.'
            . strtolower($this->format);
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

    /**
     * Get file name with extension
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->name.'.'.strtolower($this->format);
    }
}
