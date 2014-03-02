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
use Silex\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Thumbnail generation command
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class GenerateThumbnails extends Command
{
    /**
     * Application instance
     * @var Application
     */
    private $app;

    /**
     * Constructor
     */
    public function __construct($name, Application $app)
    {
        parent::__construct('generate:thumbnails');
        $this->app = $app;
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<fg=green>Generating all book thumbnails</fg=green>');

        $allBooks = $this->app['model.book']->getCollection()->getLatest(999999);

        // Progress bar
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($output, $allBooks->count());

        // Generate each book thumbnail
        foreach($allBooks as &$book) {

            $cover = $book->getCover();

            $cover->getThumbnailPath(160, 260);
            $cover->getThumbnailPath(80, 120);

            $progress->advance();
        }

        $progress->finish();

        $output->writeln('');
        $output->writeln('<fg=green>Done !</fg=green>');
        $output->writeln('');

        return 1;
    }
}
