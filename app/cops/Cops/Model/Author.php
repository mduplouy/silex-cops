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
 * Author model class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Author extends Common
{
    /**
     * Resource name
     * @var string
     */
    protected $_resourceName = 'Resource\\Author';

    /**
     * Get the latest added books
     *
     * @return array Array of Book object
     */
    public function getAggregatedList()
    {
        $output = array();
        foreach($this->getResource()->getAggregatedList($this) as $author) {
            // Force non alpha to #
            if (!preg_match('/[A-Z]/', $author['first_letter'])) {
                $author['first_letter'] = '#';
            }
            $output[$author['first_letter']] = $author['nb_author'];
        }
        return $output;
    }

}
