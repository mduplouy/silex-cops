<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model;

use Cops\Model\Core;

/**
 * Book model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Book extends Common
{
    /**
     * Resource name
     * @var string
     */
    protected $_resourceName = 'Resource\\Book';

    /**
     * A Cover object instance
     * @var \Cops\Model\Cover
     */
    protected $_cover;

    /**
     * A Serie object instance
     * @var \Cops\Model\Serie
     */
    protected $_serie;

    /**
     * Get the latest added books
     *
     * @return array Array of Book object
     */
    public function getLatest()
    {
        $output = array();
        foreach($this->getResource()->getLatest($this) as $bookData) {
            // Remove html code from the comments
            $bookData['comment'] = strip_tags($bookData['comment']);

            $book = clone($this);
            $book->setData($bookData);

            $output[] = $book;
        }
        return $output;
    }

    /**
     * Load book
     *
     * @param int $bookId
     *
     * @return Cops\Model\Book
     */
    public function load($bookId)
    {
        return $this->getResource()->load($bookId, $this);
    }

    /**
     * Cover object getter
     *
     * @return Cops\Model\Cover
     */
    public function getCover()
    {
        if (is_null($this->_cover)) {
            $this->_cover = $this->getModel('Cover', $this);
        }
        return $this->_cover;
    }

    /**
     * Serie object getter
     *
     * @return Cops\Model\Serie
     */
    public function getSerie()
    {
        if (is_null($this->_serie)) {
            $this->_serie = $this->getModel('Serie', $this);
        }
        return $this->_serie;
    }
}
