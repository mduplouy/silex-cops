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
 * Entity abstract class model
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
interface EntityInterface
{
    /**
     * Set data into object
     *
     * @param array
     *
     * @return \Cops\Model\Core
     */
    public function setData(array $dataArray);

    /**
     * Resource object loader
     *
     * @return \Cops\Model\ResourceAbstract
     */
    public function getResource();

    /**
     * Collection object loader
     *
     * @return \Cops\Model\Collection
     */
    public function getCollection();
}
