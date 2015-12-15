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

use Cops\Core\AbstractApplicationAware;
use Cops\Core\Archive\AdapterInterface;
use Cops\Core\Entity\BookFile\BookFileCollection;
use Cops\Core\Entity\BookFile\AdapterInterface as BookFileAdapterInterface;

/**
 * Abstract archive adapter
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractAdapter extends AbstractApplicationAware implements AdapterInterface
{
    /**
     * Current archive files
     * @var BookFileCollection
     */
    protected $files;

    /**
     * Constructor
     *
     * @param BookFileCollection $files
     */
    public function __construct(BookFileCollection $files)
    {
        $this->files = $files;
    }

    /**
     * Set current archive files
     *
     * @param BookFileCollection $bookfiles
     *
     * @return self
     */
    public function setFiles(BookFileCollection $bookfiles)
    {
        $this->files = $bookfiles;

        return $this;
    }

    /**
     * Add file to current archive
     *
     * @param BookFileAdapterInterface $bookfile
     *
     * @return self
     */
    public function addFile(BookFileAdapterInterface $bookfile)
    {
        $this->files->add($bookfile);

        return $this;
    }

    /**
     * Add files to current archive
     *
     * @param BookFileCollection $bookfiles
     *
     * @return self
     */
    public function addFiles(BookFileCollection $bookfiles)
    {
        foreach ($bookfiles as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * Delete file on request terminate
     *
     * @param string The path to archive file
     *
     * @return void
     */
    protected function deleteOnRequestTerminate($file)
    {
        $this->app->finish(function () use ($file) {
            if (php_sapi_name() != 'cli') {
                unlink($file);
            }
        });
    }
}
