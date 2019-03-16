<?php
namespace App\Notifications;

use GuzzleHttp\Client;

class TelegramSender
{
    const METHOD_SEND_MESSAGE = 'sendMessage';

    /** @var Client  */
    private $client;

    /** @var string  */
    private $telegramUrl;

    /** @var string  */
    private $telegramBotToken;

    /**
     * TelegramSender constructor.
     * @param string $telegramUrl
     * @param string $telegramBotToken
     * @param Client $client
     */
    public function __construct(string $telegramUrl, string $telegramBotToken, Client $client)
    {
        $this->client = $client;
        $this->telegramUrl = $telegramUrl;
        $this->telegramBotToken = $telegramBotToken;
    }

    public function sendMessage($chatId, $text)
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
        ];

        $this->client->request(
            'POST',
            $this->getUrl(self::METHOD_SEND_MESSAGE),
            [
                'body' => json_encode($params),
                'headers' => ['Content-Type' => 'application/json']
            ]
        );
    }

    /**
     * @param $method
     * @return string
     */
    private function getUrl($method): string
    {
        return "{$this->telegramUrl}/bot{$this->telegramBotToken}/${method}";
    }
}