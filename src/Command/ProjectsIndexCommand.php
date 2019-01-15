<?php

namespace App\Command;

use App\Search\ProjectIndexer\ProjectIndexerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectsIndexCommand extends Command
{
    /** @var ProjectIndexerInterface */
    private $projectIndexer;

    /**
     * ProjectsIndexCommand constructor.
     *
     * @param string                  $name
     * @param ProjectIndexerInterface $projectIndexer
     */
    public function __construct($name = null, ProjectIndexerInterface $projectIndexer)
    {
        parent::__construct($name);
        $this->projectIndexer = $projectIndexer;
    }

    protected function configure()
    {
        $this
            ->setName('projects:index')
            ->setDescription('Indexing all projects.')
            ->setHelp('This command will create search indexes for all projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting index...');
        $this->projectIndexer->indexAll();
        $output->writeln('<info>Done!</info>');
    }
}
