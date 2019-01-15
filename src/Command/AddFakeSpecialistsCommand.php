<?php

namespace App\Command;

use App\Testing\TestSpecialistCreator;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddFakeSpecialistsCommand extends ContainerAwareCommand
{
    /**
     * @var TestSpecialistCreator
     */
    private $testSpecialistCreator;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct($name = null, TestSpecialistCreator $testSpecialistsCreator, UserManagerInterface $userManager)
    {
        parent::__construct($name);
        $this->testSpecialistCreator = $testSpecialistsCreator;
        $this->userManager = $userManager;
    }

    protected function configure()
    {
        $this
            ->setName('test:specialists:add')
            ->setDescription('Add test specialists to the platform.')
            ->setHelp('This command allows you to create test specialists. Example: test:specialists:add-to-user 1')
            ->addArgument('specialists_count', InputArgument::REQUIRED, 'how many specialists');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $specialistsCount = (int) $input->getArgument('specialists_count');
        for ($i = 0; $i < $specialistsCount; ++$i) {
            $user = $this->testSpecialistCreator->create();
            $output->writeln(sprintf("%d)\t%s", ($i + 1), $user->getFullName()));
        }
        $output->writeln('<info>Done!</info>');
    }
}
