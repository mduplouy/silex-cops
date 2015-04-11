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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Internal database init command
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class InitDatabase extends Command
{
    /**
     * Application instance
     * @var \Silex\Application
     */
    private $app;

    /**
     * Constructor
     *
     * @param string     $name  Command name
     * @param Appliction $app   Silex app instance
     */
    public function __construct($name, Application $app)
    {
        parent::__construct('database:init');
        $this->app = $app;
        $this->setDescription('Init SilexCops internal database');
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('Initializing database');
        $output->writeln('');

        $config = $this->app['config'];

        $targetDb = getcwd().DS.$config->getValue('internal_db');
        $output->writeLn(sprintf('<fg=green>Target file : %s </fg=green>', $targetDb));

        $helper = $this->getHelperSet()->get('question');

        $questionText = 'Are you sure you want to init database ? All your user account will be lost. (y/[n]) ';
        $question = new ConfirmationQuestion($questionText, false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<fg=green>Exiting, no modification done</fg=green>');
            return;
        }

        $output->writeln('Creating new database schema');

        $this->app['repository.user']->dropTable()->createTable();

        $output->writeln(
            sprintf(
                '<fg=green>Done ! Admin account was reset to </fg=green> %s:%s',
                $this->app['config']->getValue('default_login'),
                $this->app['config']->getValue('default_password')
            )
        );
        $output->writeln('');

        $this->app['repository.user-book']->dropTable()->createTable();

        return 1;
    }
}
