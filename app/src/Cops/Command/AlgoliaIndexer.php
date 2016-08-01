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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Algolia indexer command
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class AlgoliaIndexer extends AbstractProcessBookCommand
{
    /**
     * Algolia search adapter instance
     * @var \Cops\Core\Search\Adapter\Algolia
     */
    private $algolia;

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

        $this->algolia = $app['factory.search']->getInstance('algolia');
    }

    /**
     * Launched before executing processing
     *
     * @param OutputInterface $output
     * @param string          $dbName
     *
     * @return void
     */
    protected function beforeBookProcessing(OutputInterface $output, $dbName)
    {
        $output->writeln(sprintf('<fg=green>Reindex all books from "%s" database</fg=green>', $dbName));
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
        $this->algolia->indexBooks($books);

        $progressBar->advance($books->count());
    }
}
