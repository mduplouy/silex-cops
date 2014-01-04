<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Model\Tag;

use Cops\Exception\TagException;
use Cops\Model\Core;
use Cops\Model\Tag;
use \PDO;

/**
 * Tag resource model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Resource extends \Cops\Model\Resource
{

    /**
     * Load a tag data
     *
     * @param  int              $tagId
     * @param  \Cops\Model\Tag  $tag
     *
     * @return \Cops\Model\Book;
     */
    public function load($tagId, Tag $tag)
    {
        /**
         * Load book common informations
         */
        $sql = 'SELECT
            main.id,
            main.name,
            COUNT(books_tags_link.id) as book_count
            FROM tags AS main
            LEFT OUTER JOIN books_tags_link ON (
                main.id = books_tags_link.tag
            )
            WHERE main.id = ?
            GROUP BY main.id';

        $result = $this->getConnection()->fetchAssoc(
            $sql,
            array(
                (int) $tagId,
            )
        );

        if (empty($result)) {
            throw new TagException(sprintf('Tag width id %s not found', $tagId));
        }

        return $tag->setData($result);
    }

    /**
     * Count book number in serie
     *
     * @param  int $tagId
     *
     * @return int
     */
    public function countBooks($tagId)
    {
        $sql = 'SELECT
            COUNT(*) FROM tags
            INNER JOIN books_tags_link ON tags.id = books_tags_link.tag
            WHERE tags.id = ?';

        return (int) $this->getConnection()
            ->fetchColumn(
                $sql,
                array(
                    (int) $tagId,
                ),
                0
            );
    }
}