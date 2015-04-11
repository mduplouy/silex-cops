<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\BookFile\Adapter;

use Cops\Core\Entity\BookFile\AbstractAdapter;
use Cops\Core\Entity\BookFile\AdapterInterface;

/**
 * PDF adpater model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Pdf extends AbstractAdapter implements AdapterInterface
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
