<?php

namespace Brofist\RabbitMq\Consumer;

use Brofist\RabbitMq\Client\AbstractWorker;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer extends AbstractWorker
{
    /**
     * @var ConsumerActionInterface
     */
    protected $consumerAction;

    /**
     * @var bool
     */
    protected $requeueAfterFailure = false;

    public function consume(ConsumerActionInterface $consumerAction, string $queue, bool $requeueAfterFailure = false)
    {
        $this->consumerAction = $consumerAction;
        $this->requeueAfterFailure = $requeueAfterFailure;
        $channel = $this->getClient()->getChannel();
        $this->setPcntlAlarm(5);

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            [$this, 'callbackMethod']
        );

        $this->resetPcntlAlarm();

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $this->client->closeConnection();
    }

    public function callbackMethod(AMQPMessage $message)
    {
        if ($this->consumerAction->consumerActionExecute($message->getBody())) {
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        } else {
            $message->delivery_info['channel']->basic_reject($message->delivery_info['delivery_tag'], $this->requeueAfterFailure);
        }
    }
}
