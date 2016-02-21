<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity\Book;

use Cops\Core\Entity\Book as BookEntity;

/**
 * Editable book entity
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class EditableBook extends BookEntity
{
    /**
     * Repository interface to be checked
     */
    const REPOSITORY_INTERFACE = 'Cops\Core\Entity\RepositoryInterface\Book\EditableBookRepositoryInterface';

    /**
     * Update author
     *
     * @param string|array $authors
     *
     * @return bool
     */
    public function updateAuthor($authors)
    {
        if (!is_array($authors)) {
            $authors = explode('&', $authors);
        }

        return $this->getRepository()->updateAuthor($this->getId(), $authors);
    }

    /**
     * Update title
     *
     * @param string $title
     *
     * @return bool
     */
    public function updateTitle($title)
    {
        if ($output = $this->getRepository()->updateTitle($this->getId(), $title)) {
            $this->setTitle($title);
        }

        return $output;
    }

    /**
     * Update publication date
     *
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    public function updatePubDate(\DateTime $dateTime)
    {
        return $this->getRepository()->updatePubDate($this->getId(), $dateTime);
    }

    /**
     * Update book comment
     *
     * @param string $comment
     *
     * @return bool
     */
    public function updateComment($comment)
    {
        return $this->getRepository()->updateComment($this->getId(), $comment);
    }
}
