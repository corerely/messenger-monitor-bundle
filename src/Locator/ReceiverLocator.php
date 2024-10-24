<?php

namespace Corerely\MessengerMonitorBundle\Locator;

use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final readonly class ReceiverLocator
{
    public function __construct(
        private ServiceProviderInterface $receiverLocator,
        private array                    $receiverNames,
    ) {
    }

    /**
     * Key-Value array of receiver name to receiver object.
     *
     * @return array<string, ReceiverInterface>
     */
    public function getReceiversMapping(): array
    {
        $receivers = [];
        foreach ($this->receiverNames as $receiverName) {
            $receivers[$receiverName] = $this->getReceiver($receiverName);
        }

        return $receivers;
    }

    public function getReceiver(string $receiverName): ReceiverInterface
    {
        if (! \in_array($receiverName, $this->receiverNames, true) || ! $this->receiverLocator->has($receiverName)) {
            throw new \Exception(sprintf('Receiver "%s" does not exist.', $receiverName));
        }

        return $this->receiverLocator->get($receiverName);
    }
}
