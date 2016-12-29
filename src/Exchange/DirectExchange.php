<?php

namespace Brofist\RabbitMq\Exchange;

use Brofist\RabbitMq\Client\Client;

class DirectExchange extends AbstractExchange
{
    public function __construct(Client $client, string $name)
    {
        parent::__construct($client, $name);
        $this->type = static::EXCHANGE_TYPE_DIRECT;
    }
}
