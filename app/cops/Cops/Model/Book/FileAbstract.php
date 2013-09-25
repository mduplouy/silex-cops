<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Book;

use Cops\Model\Common;
use Cops\Model\Book\FileAbstract;
use Cops\Model\Book\FileInterface;

/**
 * Book file abstract class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class FileAbstract extends Common implements FileInterface
{
    /**
     * File types
     */
    const TYPE_EPUB = 'epub';
    const TYPE_PDF  = 'pdf';

    /**
     * File types storage
     * @var array
     */
    protected $_fileTypes = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_fileTypes[self::TYPE_EPUB] = self::TYPE_EPUB;
        $this->_fileTypes[self::TYPE_PDF]  = self::TYPE_PDF;
    }

    /**
     * File types getter
     *
     * @return array
     */
    public function getFileTypes()
    {
        return $this->_fileTypes;
    }

}
