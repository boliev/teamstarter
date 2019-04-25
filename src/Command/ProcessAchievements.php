<?php

namespace App\Command;

use App\Achievements;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessAchievements extends Command
{
    /** @var UserRepository  */
    private $userRepository;

    /** @var Achievements\Factory  */
    private $achievementsFactory;

    public function __construct(
        $name = null,
        UserRepository $userRepository,
        Achievements\Factory $achievementsFactory
    )
    {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->achievementsFactory = $achievementsFactory;
    }

    protected function configure()
    {
        $this
            ->setName('achievements:process')
            ->setDescription('Process achievements for users.')
            ->setHelp('This command will process achievements for users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->userRepository->getEnabled();
        $achievements = $this->achievementsFactory->getAchievements();
        /** @var User $user */
        foreach($users as $user) {
            foreach($achievements as $achievement) {
                if($achievement->isNeeded($user)) {
                    $achievement->apply($user);
                }
            }
        }
    }
}
