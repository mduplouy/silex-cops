<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity;

use Cops\Core\AbstractCollection;
use Cops\Core\Application;

/**
 * Author collection
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AuthorCollection extends AbstractCollection
{
    /**
     * Find by first letter
     *
     * @param  string $letter
     *
     * @return $this
     */
    public function findByFirstLetter($letter)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByFirstLetter($letter)
        );
    }

    /**
     * Get collection based on bookId
     *
     * @param  int   $bookId
     *
     * @return $this
     */
    public function findByBookId($bookId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->loadByBookId($bookId)
        );
    }

    /**
     * Get concatened author's name
     *
     * @return string
     */
    public function getName()
    {
        $name = array();
        foreach ($this as $author) {
            $name[] = trim($author->getName());
        }
        return implode(' & ', $name);
    }

    /**
     * Find from book collection
     *
     * @param BookCollection $books
     *
     * @return AuthorCollection
     */
    public function findFromBooks(BookCollection $books)
    {
        if ($books->count()) {
            $this->setDataFromArray(
                $this->getRepository()->loadFromBooks($books)
            );
        }

        return $this;
    }

    /**
     * Count all authors
     *
     * @return int
     */
    public function countAll()
    {
        return $this->getRepository()->countAll();
    }

    /**
     * Count authors nb grouped by first letter
     *
     * @return array
     */
    public function countGroupedByFirstLetter(\Silex\Application $app)
    {
        $output = array();
        foreach ($this->getRepository()->countGroupedByFirstLetter() as $author) {
            // Force non alpha to #
            if (!preg_match('/[A-Z'.($app['config']->getValue('add_cap_letters')).']/', $author['first_letter'])) {
                $author['first_letter'] = '#';
            }
            if (!array_key_exists($author['first_letter'], $output)) {
                $output[$author['first_letter']] = 0;
            }

            $output[$author['first_letter']] += $author['nb_author'];
        }

        return $output;
    }
}
