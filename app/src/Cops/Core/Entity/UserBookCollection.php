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
use Cops\Core\CollectionableInterface;
use Cops\Core\Entity\UserBookInterface;

/**
 * User book collection
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserBookCollection extends AbstractCollection
{
    /**
     * Book Ids
     * @var array
     */
    protected $bookIds = array();

    /**
     * Try to get book silently
     *
     * @param int    $userId
     * @param int    $bookId
     * @param string $action
     *
     * @return \Cops\Core\Entity\UserBook|false
     */
    public function getBook($userId, $bookId, $action)
    {
        try {
            return $this->getById($userId.$action.$bookId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     *
     * Overloaded to get book ids
     *
     * @throws \InvalidArgumentException
     */
    public function add(CollectionableInterface $element)
    {
        if (!$element instanceof UserBookInterface) {
            throw new \InvalidArgumentException('Element must implement UserBookInterface');
        }

        parent::add($element);
        $this->bookIds[] = $element->getBookId();

        return $this;
    }

    /**
     * Get all book ids
     *
     * @return array
     */
    public function getAllBookIds()
    {
        return $this->bookIds;
    }

    /**
     * Has one by action
     *
     * @param string $action
     *
     * @return bool
     */
    public function hasOneByAction($action)
    {
        foreach ($this as $element) {
            if ($element->getAction() == $action) {
                return true;
            }
        }

        return false;
    }


    /**
     * Find from user id
     *
     * @param int $userId
     *
     * @return $this
     */
    public function findFromUserId($userId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findFromUserId($userId)
        );
    }

    /**
     * Find from user id and action
     *
     * @param int    $userId
     * @param string $action
     *
     * @return $this
     */
    public function findFromUserIdAndAction($userId, $action)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findFromUserIdAndAction($userId, $action)
        );
    }

    /**
     * Find from book id and user id
     *
     * @param int $bookId
     * @param int $userId
     *
     * @return $this
     */
    public function findFromBookIdAndUserId($bookId, $userId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findFromBookIdAndUserId($bookId, $userId)
        );
    }

    /**
     * Delete existing collection content
     *
     * @return void
     */
    public function delete()
    {
        $repository = $this->getRepository();

        foreach ($this as $book) {
            $repository->delete($book);
        }
    }
}
