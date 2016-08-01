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
 * Thumbnail generation command
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class GenerateThumbnails extends AbstractProcessBookCommand
{
    /**
     * Constructor
     *
     * @param string      $name
     * @param Application $app
     */
    public function __construct($name, Application $app)
    {
        parent::__construct('generate:thumbnails', $app);
        $this->setDescription('Generate the thumbnails for every book');
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
        $output->writeln(sprintf('<fg=green>Generating all book thumbnails for "%s" database</fg=green>', $dbName));
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
        foreach ($books as $book) {

            $cover = $book->getCover();

            $cover->getThumbnailPath(160, 260);
            $cover->getThumbnailPath(80, 120);

            $progressBar->advance();
        }
    }
}
