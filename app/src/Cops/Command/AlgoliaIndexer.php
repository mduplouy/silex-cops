<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Command;

use Cops\Core\Application;
use Cops\Core\Entity\BookCollection;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Algolia indexer command
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AlgoliaIndexer extends AbstractProcessBookCommand
{
    /**
     * Chunk size
     * @var int
     */
    const CHUNK_SIZE = 100;

    /**
     * Algolia index instance
     * @var \AlgoliaSearch\Index
     */
    private $algoliaIndex;

    /**
     * Constructor
     *
     * @param string      $name
     * @param Application $app
     */
    public function __construct($name, Application $app)
    {
        parent::__construct('algolia:reindex', $app);
        $this->setDescription('Update algolia index by sending all books information');

        $this->algoliaIndex = $app['algolia'];
    }

    /**
     * Process books
     *
     * @param BookCollection $books
     * @param ProgressBar    $progressBar
     *
     * @return void
     */
    protected function doProcessBooks(BookCollection $books, ProgressBar $progressBar)
    {
        $i = 0;
        $chunk = array();

        foreach ($books as $book) {
            $chunk[$i] = $book->jsonSerialize();
            $chunk[$i]['objectID'] = $book->getId();
            $i++;

            if ($i == self::CHUNK_SIZE) {
                $this->algoliaIndex->addObjects($chunk);
                $chunk = array();
                $i = 0;
            }

            $progressBar->advance();
        }

        if (!empty($chunk)) {
            $this->algoliaIndex->addObjects($chunk);
        }
    }
}
