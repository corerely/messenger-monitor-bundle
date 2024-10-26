<?php
declare(strict_types=1);

namespace Corerely\MessengerMonitorBundle\Failed;

final readonly class FailedMessageRepository
{
    public function __construct(
        private FailureReceiverProvider $failureReceiverProvider,
    ) {
    }

    public function get(int|string $id): FailedMessage
    {
        $envelope = $this->failureReceiverProvider->getFailureReceiver()->find($id);

        return FailedMessage::fromEnvelope($envelope);
    }

    /**
     * @return FailedMessage[]
     */
    public function listFailedMessages(int $limit, bool $latestFirst = true): array
    {
        $envelopes = $this->failureReceiverProvider->getFailureReceiver()->all($limit);

        $messages = [];

        foreach ($envelopes as $envelope) {
            $messages[] = FailedMessage::fromEnvelope($envelope);
        }

        if ($latestFirst) {
            $messages = array_reverse($messages);
        }

        return $messages;
    }
}
