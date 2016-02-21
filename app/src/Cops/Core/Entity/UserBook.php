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

use Cops\Core\AbstractEntity;
use Cops\Core\CollectionableInterface;

/**
 * User books
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserBook extends AbstractEntity implements CollectionableInterface, UserBookInterface
{
    /**
     * Repository interface to be checked
     */
    const REPOSITORY_INTERFACE = 'Cops\Core\Entity\RepositoryInterface\UserBookRepositoryInterface';

    /**
     * Actions
     */
    const ACTION_DOWNLOAD = 'to_download';
    const ACTION_TOREAD   = 'to_read';
    const ACTION_FAVORITE = 'favorite';

    /**
     * Available actions
     * @var array
     */
    protected $availableActions = array(
        self::ACTION_DOWNLOAD => 'Add to download cart',
        self::ACTION_FAVORITE => 'Add to favorites',
        self::ACTION_TOREAD   => 'Mark as to read',
    );

    /**
     * Available lists
     * @var array
     */
    protected $availableLists = array(
        self::ACTION_DOWNLOAD => 'Download cart',
        self::ACTION_FAVORITE => 'Favorites',
        self::ACTION_TOREAD   => 'To read',
    );

    /**
     * User ID
     * @var int
     */
    private $userId;

    /**
     * Action
     * @var string
     */
    private $action;

    /**
     * Book ID
     * @var int
     */
    private $bookId;

    /**
     * Save to database
     *
     * @return bool
     */
    public function save()
    {
        return $this->getRepository()->insert($this);
    }

    /**
     * Delete from database
     *
     * @return bool
     */
    public function delete()
    {
        return $this->getRepository()->delete($this);
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return sprintf(
            '%s%s%s',
            $this->getUserId(),
            $this->getAction(),
            $this->getBookId()
        );
    }

    /**
     * Set action
     *
     * @param  string $action
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setAction($action)
    {
        if (!array_key_exists($action, $this->getAvailableActions())) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid action provided : %s, available actions are %s',
                $action,
                implode(', ', $this->getAvailableActions())
            ));
        }

        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get available actions
     *
     * @return array
     */
    public function getAvailableActions()
    {
        return $this->availableActions;
    }

    /**
     * Get available lists
     *
     * @return array
     */
    public function getAvailableLists()
    {
        return $this->availableLists;
    }

    /**
     * Set bookId
     *
     * @param  int $bookId
     *
     * @return $this
     */
    public function setBookId($bookId)
    {
        $this->bookId = (int) $bookId;

        return $this;
    }

    /**
     * Get book ID
     *
     * @return int
     */
    public function getBookId()
    {
        return $this->bookId;
    }

    /**
     * Set user id
     *
     * @param  int   $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = (int) $userId;

        return $this;
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
