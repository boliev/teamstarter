<?php

namespace App\Command;

use App\Search\SpecialistIndexer\SpecialistIndexerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SpecialistsIndexCommand extends ContainerAwareCommand
{
    /** @var SpecialistIndexerInterface */
    private $specialistIndexer;

    /**
     * ProjectsIndexCommand constructor.
     *
     * @param string                     $name
     * @param SpecialistIndexerInterface $specialistIndexer
     */
    public function __construct($name = null, SpecialistIndexerInterface $specialistIndexer)
    {
        parent::__construct($name);
        $this->specialistIndexer = $specialistIndexer;
    }

    protected function configure()
    {
        $this
            ->setName('specialists:index')
            ->setDescription('Indexing all specialists.')
            ->setHelp('This command will create search indexes for all specialists');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting index...');
        $this->specialistIndexer->indexAll();
        $output->writeln('<info>Done!</info>');
    }
}
