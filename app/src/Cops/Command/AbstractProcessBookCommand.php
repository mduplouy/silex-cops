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
 * Abstract command class for book processing
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
abstract class AbstractProcessBookCommand extends Command
{
    /**
     * Option value for all databases
     */
    const OPTION_ALL_DB = 'all';

    /**
     * Application instance
     * @var Application
     */
    protected $app;

    /**
     * Constructor
     *
     * @param string      $name
     * @param Application $app
     */
    public function __construct($name, Application $app)
    {
        parent::__construct($name);
        $this->app = $app;
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

        $allDbs = $this->app['config']->getValue('db_name');
        unset($allDbs[Application::INTERNAL_DB_KEY]);

        if ($selectedDb == self::OPTION_ALL_DB) {
            foreach ($allDbs as $db => $path) {
                $this->processBooksFromDatabase($output, $db);
            }
        } else {
            if (!array_key_exists($selectedDb, $allDbs)) {
                throw new \InvalidArgumentException(
                    sprintf('Database %s does not exists', $selectedDb)
                );
            }

            $output->writeln('');

            $this->processBooksFromDatabase($output, $selectedDb);
        }

        return 1;
    }

    /**
     * Process the books
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    private function processBooksFromDatabase(OutputInterface $output, $dbName)
    {
        $this->app['config']->setDatabaseKey($this->app, $dbName);

        $this->beforeBookProcessing($output, $dbName);

        $books = $this->app['collection.book'];

        $totalBooks = $books->countAll();
        $page = 1;
        $pageSize = 200;

        // Progress bar
        $progress = new ProgressBar($output);
        $progress->start($totalBooks);

        $done = false;

        while (!$done) {
            $allBooks = $this->app['collection.book']
                ->setFirstResult(($page-1) * $pageSize)
                ->setMaxResults($pageSize)
                ->findAll()
                ->addAuthors($this->app['collection.author'])
                ->addTags($this->app['collection.tag'])
                ->addBookFiles($this->app['collection.bookfile'])
            ;

            $this->doProcessBooks($allBooks, $progress);

            if ($page * $pageSize >= $totalBooks) {
                $done = true;
            }

            $page++;
        }

        $progress->finish();

        $output->writeln('');
        $output->writeln('<fg=green>Done !</fg=green>');
        $output->writeln('');
    }

    /**
     * Do the process on books
     *
     * @param  BookCollection $books
     * @param  ProgressBar    $progressBar
     *
     * @return void
     */
    abstract protected function doProcessBooks(BookCollection $books, ProgressBar $progressBar);

    /**
     * Launched before executing processing
     *
     * @param  OutputInterface $output
     * @param  string          $dbName
     *
     * @return void
     */
    abstract protected function beforeBookProcessing(OutputInterface $output, $dbName);
}
