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
 * tar.gz archive adapter
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class TarGz extends AbstractAdapter implements AdapterInterface
{
    /**
     * Get file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return '.tgz';
    }

    /**
     * Generate archive
     *
     * @implements ArchiveInterface
     *
     * @return string The path to archive file
     */
    public function generateArchive()
    {
        $archive = tempnam(sys_get_temp_dir(), '').'.tar';

        $phar = new \PharData($archive, \Phar::TAR);
        foreach ($this->files as $file) {
            if (file_exists($file->getFilePath())) {
                $phar->addFile($file->getFilePath(), $file->getFileName());
            }
        }

        $fileName = $archive.'.tgz';

        file_put_contents($fileName , gzencode(file_get_contents($archive)));

        // Remove uncompressed tar & archive
        $this->deleteOnRequestTerminate($archive);
        $this->deleteOnRequestTerminate($fileName);

        return $fileName;
    }
}
