<?php

namespace Corerely\MessengerMonitorBundle\Failed;

use Corerely\MessengerMonitorBundle\Locator\ReceiverLocator;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

final readonly class FailureReceiverProvider
{

    public function __construct(
        private ReceiverLocator $receiverLocator,
        private ?string         $failureReceiverName = null,
    ) {
    }

    public function getFailureReceiver(): ListableReceiverInterface
    {
        if (! $this->failureReceiverName) {
            throw new \Exception('Failure receiver does not exist');
        }

        $failureReceiver = $this->receiverLocator->getReceiver($this->failureReceiverName);

        if (! $failureReceiver instanceof ListableReceiverInterface) {
            throw new \Exception('Failure receiver is not listable');
        }

        return $failureReceiver;
    }
}
