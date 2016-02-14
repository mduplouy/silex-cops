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
     */
    public function findById($serieId)
    {
        $this->setId($serieId);

        $result = $this->getRepository()->findById($serieId);

        if (empty($result)) {
            throw new SerieNotFoundException(sprintf('Serie width id %s not found', $serieId));
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
}
