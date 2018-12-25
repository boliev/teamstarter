<?php

namespace AppBundle\Sockets;

use AppBundle\Entity\Message;
use ElephantIO\Engine\SocketIO\Version1X;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Client
{
    protected $logger;

    protected $url;

    protected $port;

    protected $client;

    protected $dateFormatter;

    public function __construct(string $url, string $port, LoggerInterface $logger, RequestStack $request)
    {
        $this->logger = $logger;
        $this->url = $url;
        $this->port = $port;
        $this->client = new \ElephantIO\Client(new Version1X($this->url.':'.$this->port));
        $this->client->initialize();
        $this->dateFormatter = new \IntlDateFormatter(
            $request->getCurrentRequest()->getLocale(),
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::MEDIUM
        );
    }

    public function sendMessage(Message $message)
    {
        $this->client->emit('message', [
            'id' => $message->getId(),
            'from' => $message->getFrom()->getId(),
            'to' => $message->getTo()->getId(),
            'fromFirstName' => $message->getFrom()->getFirstName(),
            'fromProfilePicture' => $message->getFrom()->getProfilePicture() ?? null,
            'offer' => $message->getOffer()->getId(),
            'message' => $message->getMessage(),
            'createdAt' => $this->dateFormatter->format($message->getCreatedAt()),
        ]);
    }
}
