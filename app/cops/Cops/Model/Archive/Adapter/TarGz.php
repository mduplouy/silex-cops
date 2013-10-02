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
        die('not implemented yet');
    }
}