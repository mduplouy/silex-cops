<?php

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
    const resourceName = 'Resource\\Book';

    /**
     * Constructor
     *
     * @param int|null $bookId
     */
    public function __construct($bookId=null)
    {
        $this->_resource = $this->getModel(self::resourceName);
    }

    /**
     * Get the latest added books
     *
     * @return array
     */
    public function getLatest()
    {
        $this->_resource->getLatest();
    }

}
