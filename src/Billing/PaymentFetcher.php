<?php

namespace App\Billing;

class PaymentFetcher
{
    /** @var PaymentClient */
    private $client;

    public function __construct(PaymentClient $client)
    {
        $this->client = $client;
    }

    public function fetch(string $id)
    {
        return $this->client->getPayment($id);
    }
}
