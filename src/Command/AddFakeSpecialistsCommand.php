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
            ->setHelp('This command allows you to create test specialists. Example: test:specialists:add some@mail.ru')
            ->addArgument('email', InputArgument::REQUIRED, 'specialist email. Must be in src/Testing/data/specialists.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->testSpecialistCreator->create($input->getArgument('email'));
            $output->writeln('<info>Done!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }
    }
}
