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

/**
 * Book model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Book extends Common
{
    /**
     * Resource name
     * @const string
     */
    protected $_resourceName = 'Resource\\Book';

    /**
     * Get the latest added books
     *
     * @return array
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

}
