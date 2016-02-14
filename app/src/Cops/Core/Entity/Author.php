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

use Cops\Core\AbstractBookCount;
use Cops\Core\CollectionableInterface;
use Cops\Core\Calibre\Util as CalibreUtil;
use Cops\Core\Entity\Exception\AuthorNotFoundException;

/**
 * Author entity
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Author extends AbstractBookCount implements CollectionableInterface
{
    /**
     * Repository interface to be checked
     */
    const REPOSITORY_INTERFACE = 'Cops\Core\Entity\RepositoryInterface\AuthorRepositoryInterface';

    /**
     * Book ID for book linking
     * @var int
     */
    private $bookId;

    /**
     * Author name
     * @var string
     */
    private $name;

    /**
     * Sort name
     * @var string
     */
    private $sort;

    /**
     * Calibre instance for sort operations
     * @var CalibreUtil
     */
    private $calibre;

    /**
     * Constructor
     *
     * @param CalibreUtil $calibre
     */
    public function __construct(CalibreUtil $calibre)
    {
        $this->calibre = $calibre;
    }

    /**
     * Find by id
     *
     * @param  int   $authorId
     *
     * @return $this
     */
    public function findById($authorId)
    {
        $result = $this->setId($authorId)
            ->getRepository()
            ->findById($authorId);

        if (empty($result)) {
            throw new AuthorNotFoundException(sprintf('Author width id %s not found', $authorId));
        };

        return $this->setDataFromArray($result);
    }

    /**
     * Save author to DB
     *
     * @return int
     */
    public function save()
    {
        return $this->getRepository()->save($this);
    }

    /**
     * Delete author from DB
     *
     * @return bool
     */
    public function delete()
    {
        return $this->getRepository()->delete($this);
    }

    /**
     * Set bookId
     *
     * @param  int   $bookId
     *
     * @return $this
     */
    public function setBookId($bookId)
    {
        $this->bookId = (int) $bookId;

        return $this;
    }

    /**
     * Get bookId
     *
     * @return int
     */
    public function getBookId()
    {
        return $this->bookId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        if (strpos($name, '|') !== false) {
            $name = trim(implode(' ', array_reverse(explode('|', $name))));
        }
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sort
     *
     * @param string $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        if (!empty($sort)) {
            $this->sort = $sort;
        }

        return $this;
    }

    /**
     * Get sort name using Calibre algorithm
     *
     * @return string|null
     */
    public function getSort()
    {
        if (empty($this->sort) && $this->name !== null) {
            $this->sort = $this->calibre->getAuthorSortName($this->name);
        }

        return $this->sort;
    }
}
