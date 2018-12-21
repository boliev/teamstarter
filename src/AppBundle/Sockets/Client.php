<?php

namespace AppBundle\Sockets;

use AppBundle\Entity\Message;
use ElephantIO\Engine\SocketIO\Version1X;
use Psr\Log\LoggerInterface;

class Client
{
    protected $logger;

    protected $url;

    protected $port;

    protected $client;

    public function __construct(string $url, string $port, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->url = $url;
        $this->port = $port;
        $this->client = new \ElephantIO\Client(new Version1X($this->url.':'.$this->port));
        $this->client->initialize();
    }

    public function sendMessage(Message $message)
    {
        $this->client->emit('message', [
            'id' => $message->getId(),
            'from' => $message->getFrom()->getId(),
            'to' => $message->getTo()->getId(),
            'offer' => $message->getOffer()->getId(),
            'message' => $message->getMessage(),
        ]);
    }
}
