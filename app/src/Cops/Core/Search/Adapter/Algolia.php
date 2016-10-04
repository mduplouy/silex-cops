<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Search\Adapter;

use Cops\Core\Search\AbstractAdapter;
use Cops\Core\Search\AdapterInterface;
use Cops\Core\Entity\BookCollection;
use Cops\Core\Entity\Book;
use AlgoliaSearch\Index as AlgoliaIndex;
use Cops\Core\Entity\Exception\BookNotFoundException;

/**
 * Algolia search adapter class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class Algolia extends AbstractAdapter implements AdapterInterface
{
    /**
     * Chunk size for mass indexing
     * @var int
     */
    const CHUNK_SIZE = 100;

    /**
     * AlgoliaIndex instance
     * @var AlgoliaIndex
     */
    private $algoliaIndex;

    /**
     * Constructor
     *
     * @param BookCollection $bookCollection
     * @param AlgoliaIndex   $algoliaIndex
     */
    public function __construct(BookCollection $bookCollection, AlgoliaIndex $algoliaIndex)
    {
        parent::__construct($bookCollection);

        $this->algoliaIndex = $algoliaIndex;
    }

    /**
     * Get a book collection matching search results
     *
     * @param array $searchTerms
     * @param int   $nbItems
     * @param int   $page
     *
     * @return \Cops\Core\Entity\BookCollection
     *
     * @throws BookNotFoundException
     */
    public function getResults(array $searchTerms, $nbItems = 25, $page = 1)
    {
        $results = $this->algoliaIndex->search(
            implode(' ', $searchTerms),
            array(
                'attributesToRetrieve' => array('id'),
                'ignorePlurals' => true,
                'page' => $page - 1,
                'hitsPerPage' => $nbItems,
            )
        );

        if (empty($results['nbHits'])) {
            throw new BookNotFoundException('Your search matched no results');
        }

        // By default, algolia restrict browsing on the first 1000 items
        $this->bookCollection->getRepository()->setTotalRows(min($results['nbHits'], 1000));

        $bookIds = array();
        foreach ($results['hits'] as $book) {
            $bookIds[] = $book['id'];
        }

        return $this->bookCollection
            ->findById($bookIds)
            ->sortElementsById($bookIds);
    }

    /**
     * Index a single book
     *
     * @param  Book $book
     *
     * @return $this
     */
    public function indexBook(Book $book)
    {
        $this->algoliaIndex->addObject($book->jsonSerialize(), $book->getId());

        return $this;
    }

    /**
     * Index a book collection
     *
     * @param  BookCollection $bookCollection
     *
     * @return $this
     */
    public function indexBooks(BookCollection $bookCollection)
    {
        $algoliaObject = array();
        $i = 1;

        foreach ($bookCollection as $book) {

            $bookData = $book->jsonSerialize();
            $bookData['objectID'] = $book->getId();

            $algoliaObject[$i] = $bookData;

            if ($i == self::CHUNK_SIZE) {
                $this->algoliaIndex->addObjects($algoliaObject);
                $algoliaObject = array();
                $i = 0;
            }

            $i++;
        }

        if (!empty($algoliaObject)) {
            $this->algoliaIndex->addObjects($algoliaObject);
        }

        return $this;
    }
}
