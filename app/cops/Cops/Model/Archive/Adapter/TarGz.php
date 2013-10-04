<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Archive\Adapter;

use Cops\Model\ArchiveAbstract;
use Cops\Model\Archive\ArchiveInterface;

/**
 * Archive adapter for tar.gz file
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class TarGz extends ArchiveAbstract implements ArchiveInterface
{
    /**
     * Generate archive
     *
     * @return string The path to archive file
     */
    public function generateArchive()
    {
        $archive = tempnam(sys_get_temp_dir(), '').'.tar';

        $phar = new \PharData($archive, \Phar::TAR);
        foreach($this->_files as $file)
        {
            if (file_exists($file->getFilePath())) {
                $phar->addFile($file->getFilePath(), $file->getFileName());
            }
        }
        $phar->compress(\Phar::GZ);
        return $archive;
    }

    /**
     * Send headers for download
     *
     * @param  string $fileName
     * @param  string $fileSize
     *
     * @return void
     */
    public function sendHeaders($fileName, $fileSize)
    {
        header('Content-type: application/x-gzip');
        header('Content-disposition:attachment;filename="'.$fileName.'.tar.gz"');
        header('Content-Transfer-Encoding: binary');
        header("Content-length: " . $fileSize);
    }
}