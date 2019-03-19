<?php

namespace App\Command;

use App\Entity\Message;
use App\Notifications\Notificator;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewMessageNotificationsCommand extends Command
{
    /** @var MessageRepository */
    private $messageRepository;

    /** @var Notificator */
    private $notificator;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        $name = null,
        MessageRepository $messageRepository,
        Notificator $notificator,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($name);
        $this->messageRepository = $messageRepository;
        $this->notificator = $notificator;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('notifications:notify:new-messages')
            ->setDescription('Notify users about new messages by email.')
            ->setHelp('This command will send email to users if they have new messages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $messages = $this->messageRepository->getNewNotNotifiedMessages();
        $notified = [];
        /** @var Message $message */
        foreach ($messages as $message) {
            $userId = $message->getTo()->getId();
            $message->setNotificationSent(true);
            $this->entityManager->persist($message);

            if (isset($notified[$userId])) {
                $this->entityManager->flush();
                continue;
            }

            $this->notificator->newMessage($message);
            $notified[$userId] = 1;
        }

        $count = count($messages);
        if ($count > 0) {
            $this->notificator->newMessagesNotificationsWereSent($count);
        }
    }
}
