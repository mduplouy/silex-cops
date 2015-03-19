<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Archive\Adapter;

use Cops\Core\Archive\AbstractAdapter;
use Cops\Core\Archive\AdapterInterface;

/**
 * zip archive adapter
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Zip extends AbstractAdapter implements AdapterInterface
{
    /**
     * Get file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return '.zip';
    }

    /**
     * Generate archive
     *
     * @return string The path to archive file
     */
    public function generateArchive()
    {
        $archive = tempnam(sys_get_temp_dir(), '').$this->getExtension();

        $zip = new \ZipArchive;
        if ($zip->open($archive, \ZipArchive::CREATE)) {
            foreach ($this->files as $file) {
                if (file_exists($file->getFilePath())) {
                    $zip->addFile($file->getFilePath(), $file->getFileName());
                }
            }
            $zip->close();
        }

        $this->deleteOnRequestTerminate($archive);

        return $archive;
    }
}
