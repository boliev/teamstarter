<?php

namespace App\Command;

use App\Entity\User;
use App\Testing\TestProjectCreator;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddFakeProjectsCommand extends ContainerAwareCommand
{
    /**
     * @var TestProjectCreator
     */
    private $testProjectCreator;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * AddFakeProjectsCommand constructor.
     *
     * @param null                 $name
     * @param TestProjectCreator   $testProjectCreator
     * @param UserManagerInterface $userManager
     */
    public function __construct($name = null, TestProjectCreator $testProjectCreator, UserManagerInterface $userManager)
    {
        parent::__construct($name);
        $this->testProjectCreator = $testProjectCreator;
        $this->userManager = $userManager;
    }

    protected function configure()
    {
        $this
            ->setName('test:projects:add-to-user')
            ->setDescription('Add test projects to a user.')
            ->setHelp('This command allows you to create a test project. Example: test:projects:add-to-user vboliev+0103@4xxi.com 1 Published')
            ->addArgument('username', InputArgument::REQUIRED, 'User email')
            ->addArgument('project_count', InputArgument::REQUIRED, 'how many projects')
            ->addArgument('project_status', InputArgument::OPTIONAL, implode(', ', TestProjectCreator::PROGRESS_STATUSES));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userName = $input->getArgument('username');
        /** @var User $user */
        $user = $this->userManager->findUserByUsername($userName);
        if (!$user) {
            $output->writeln('<error>No such user!</error>');

            return;
        }
        $projectsCount = (int) $input->getArgument('project_count');
        for ($i = 0; $i < $projectsCount; ++$i) {
            $project = $this->testProjectCreator->create($user, $input->getArgument('project_status'));
            $output->writeln(sprintf("%d)\t%s", ($i + 1), $project->getName()));
        }
        $output->writeln('<info>Done!</info>');
    }
}
