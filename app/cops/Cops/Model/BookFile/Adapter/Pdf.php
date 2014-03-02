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

use Cops\Model\BookFile\AdapterAbstract;
use Cops\Model\BookFile\BookFileInterface;

/**
 * PDF adpater model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Pdf extends AdapterAbstract implements BookFileInterface
{
    /**
     * Get content type header for download
     *
     * @return string
     */
    public function getContentTypeHeader()
    {
        return 'application/pdf';
    }
}
