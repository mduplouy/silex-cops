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
use Cops\Core\Entity\Tag;

/**
 * Tag collection model
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class TagCollection extends AbstractCollection
{
    /**
     * Id => Key mapping for elements
     * @var array
     */
    protected $nameMapping = array();

    /**
     * @inheritDoc
     *
     * Overrided to allow name mapping
     */
    public function add(CollectionableInterface $element)
    {
        parent::add($element);

        $this->nameMapping[$element->getName()] = count($this->elements) - 1;

        return $this;
    }

    /**
     * Remove all elements
     *
     * @return self
     */
    public function clear()
    {
        parent::clear();

        $this->nameMapping = array();

        return $this;
    }

    /**
     * Get element by name
     *
     * @param string $name
     *
     * @return CollectionableInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getByName($name)
    {
        if (!array_key_exists($name, $this->nameMapping)) {
            throw new \InvalidArgumentException(sprintf(
                'Element with name %s does not exists',
                $name
            ));
        }

        return $this->getByKey($this->nameMapping[$name]);
    }

    /**
     * Load all tags along with book count
     *
     * @return TagCollection
     */
    public function findAllWithBookCount()
    {
        return $this->setDataFromArray($this->getRepository()->loadAllWithBookCount());
    }

    /**
     * Handle subtags by grouping them along with bouk count
     *
     * @param array $rawTags
     *
     * @return TagCollection
     */
    public function createSubTagsCollection(TagCollection $tags)
    {
        foreach ($this as $tag) {

            if ($tagName = strstr($tag->getName(), Tag::SUBTAG_SEPARATOR, true)) {
                $tag->setName($tagName);
            }

            try {
                $currentTag = $tags->getByName($tag->getName());
                $currentTag->setBookCount($currentTag->getBookCount() + $tag->getBookCount());
            } catch (\InvalidArgumentException $e) {
                $tags->add($tag);
            }
        }

        return $tags;
    }

    /**
     * Load all tag names
     *
     * @return TagCollection
     */
    public function findAll()
    {
        return $this->setDataFromArray(
            $this->getRepository()->loadAllNames()
        );
    }

    /**
     * Load tag collection from book id
     *
     * @param int $bookId
     *
     * @return Collection
     */
    public function findByBookId($bookId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByBookId($bookId)
        );
    }

    /**
     * Find from book collection
     *
     * @param BookCollection $books
     *
     * @return $this
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
     * Find children tag
     *
     * @param Tag $tag
     *
     * @return $this
     */
    public function findChildren(Tag $tag)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findByNamePartWithBookCount($tag->getName().Tag::SUBTAG_SEPARATOR)
        );
    }
}
