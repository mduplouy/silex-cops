<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\BookFile\Adapter;

use Cops\Model\BookFileAbstract;
use Cops\Model\BookFile\BookFileInterface;

/**
 * Kindle MOBI model adapter class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Mobi extends BookFileAbstract implements BookFileInterface
{
    /**
     * Get content type header for download
     *
     * @return string
     */
    public function getContentTypeHeader()
    {
        return 'application/octet-stream';
    }
}
