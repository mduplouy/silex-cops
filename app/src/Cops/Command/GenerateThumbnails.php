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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Cops\Core\Application;
use Cops\Core\Entity\BookCollection;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Thumbnail generation command
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class GenerateThumbnails extends Command
{
    /**
     * Option value for all databases
     */
    const OPTION_ALL_DB = 'all';

    /**
     * Application instance
     * @var Application
     */
    private $app;

    /**
     * Constructor
     *
     * @param string      $name
     * @param Application $app
     */
    public function __construct($name, Application $app)
    {
        parent::__construct('generate:thumbnails');
        $this->app = $app;
        $this->setDescription('Generate the thumbnails for every book');
    }

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'database',
            null,
            InputOption::VALUE_OPTIONAL,
            'Selected database',
            self::OPTION_ALL_DB
        );
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $selectedDb = $input->getOption('database');

        $allDbs = $this->app['config']->getValue('data_dir');
        unset($allDbs[Application::INTERNAL_DB_KEY]);

        if ($selectedDb == self::OPTION_ALL_DB) {
            foreach ($allDbs as $db => $path) {
                $this->generateThumbnailsForDb($output, $db);
            }
        } else {
            if (!array_key_exists($selectedDb, $allDbs)) {
                throw new \InvalidArgumentException(
                    sprintf('Database %s does not exists', $selectedDb)
                );
            }

            $output->writeln('');
            $this->generateThumbnailsForDb($output, $selectedDb);
        }

        return 1;
    }

    /**
     * Generate thumbnail on provided database
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    private function generateThumbnailsForDb(OutputInterface $output, $dbName)
    {
        $this->app['config']->setDatabaseKey($this->app, $dbName);

        $output->writeLn('');
        $output->writeln(sprintf('<fg=green>Generating all book thumbnails for "%s" database</fg=green>', $dbName));

        $allBooks = $this->app['collection.book']->findAll();

        // Progress bar
        $progress = new ProgressBar($output);
        $progress->start($allBooks->count());

        $this->processBooks($allBooks, $progress);

        $progress->finish();

        $output->writeln('');
        $output->writeln('<fg=green>Done !</fg=green>');
        $output->writeln('');
    }

    /**
     * Process books
     *
     * @param BookCollection $books
     * @param ProgressBar    $progressBar
     *
     * @return void
     */
    private function processBooks(BookCollection $books, ProgressBar $progressBar)
    {
        foreach ($books as $book) {

            $cover = $book->getCover();

            $cover->getThumbnailPath(160, 260);
            $cover->getThumbnailPath(80, 120);

            $progressBar->advance();
        }
    }
}
