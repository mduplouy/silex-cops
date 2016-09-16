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
use Cops\Core\Entity\Exception\SerieNotFoundException;

/**
 * Serie model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Serie extends AbstractBookCount implements CollectionableInterface
{
    /**
     * Repository interface to be checked
     */
    const REPOSITORY_INTERFACE = 'Cops\Core\Entity\RepositoryInterface\SerieRepositoryInterface';

    /**
     * Serie name
     * @var string
     */
    private $name;

    /**
     * Sort name
     * @var string
     */
    private $sort;

    /**
     * Find by id
     *
     * @param  int   $serieId
     *
     * @return $this
     *
     * @throws SerieNotFoundException
     */
    public function findById($serieId)
    {
        $this->setId($serieId);

        $result = $this->getRepository()->findById($serieId);

        if (empty($result)) {
            throw new SerieNotFoundException(sprintf('Serie with id %s not found', $serieId));
        }

        return $this->setDataFromArray($result);
    }

    /**
     * Find by name
     *
     * @param  string $name
     *
     * @return $this
     */
    public function findByName($name)
    {
        $result = $this->getRepository()->findByName($name);

        if (empty($result)) {
            throw new SerieNotFoundException(sprintf('Serie with name %s was not found', $name));
        }

        return $this->setDataFromArray($result);
    }

    /**
     * Set name
     *
     * @param  string $name
     *
     * @return $this
     */
    public function setName($name)
    {
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
     * @param  string $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Associate current serie to given book
     *
     * @param  Book   $book
     *
     * @return bool
     */
    public function associateToBook(Book $book)
    {
        if (!$this->getId()) {
            try {
                $this->findByName($this->getName());
            } catch (SerieNotFoundException $e) {
                // Do noting it's a new serie
            }
        }

        return (bool) $this->getRepository()
            ->associateToBook($this, $book);
    }
}
