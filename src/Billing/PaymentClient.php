<?php

namespace App\Billing;

use App\Exception\PaymentFailureException;
use GuzzleHttp\Client;

class PaymentClient
{
    private const URL_YANDEX_CREATE_PAYMENT = 'https://payment.yandex.net/api/v3/payments';

    /** @var Client */
    private $client;

    /** @var string */
    private $key;

    /** @var string */
    private $shopId;

    public function __construct(string $shopId, string $key, Client $client)
    {
        $this->client = $client;
        $this->shopId = $shopId;
        $this->key = $key;
    }

    /**
     * @param int    $price
     * @param string $currency
     * @param string $returnUrl
     * @param string $description
     *
     * @return array
     *
     * @throws PaymentFailureException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPayment(int $price, string $currency, string $returnUrl, string $description): array
    {
        $payment = [
            'amount' => [
                'value' => $price,
                'currency' => $currency,
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $returnUrl,
            ],
            'capture' => true,
            'description' => $description,
        ];

        return $this->makePostRequest(self::URL_YANDEX_CREATE_PAYMENT, $payment);
    }

    /**
     * @param string $url
     * @param $data
     *
     * @return array
     *
     * @throws PaymentFailureException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function makePostRequest(string $url, $data): array
    {
        $res = $this->client->request('POST', $url, [
            'auth' => [$this->shopId, $this->key],
            'headers' => [
                'Content-Type' => 'application/json',
                'Idempotence-Key' => uniqid(),
            ],
            'body' => json_encode($data),
        ]);

        if (200 !== $res->getStatusCode()) {
            throw new PaymentFailureException($res->getBody(), $res->getStatusCode());
        }

        return json_decode($res->getBody(), true);
    }
}
