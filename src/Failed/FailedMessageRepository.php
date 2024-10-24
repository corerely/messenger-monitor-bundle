<?php
declare(strict_types=1);

namespace Corerely\MessengerMonitorBundle\Failed;

final readonly class FailedMessageRepository
{
    public function __construct(
        private FailureReceiverProvider $failureReceiverProvider,
    ) {
    }

    /**
     * @return iterable<FailedMessage>
     */
    public function listFailedMessages(int $limit, bool $latestFirst = true): iterable
    {
        $envelopes = $this->failureReceiverProvider->getFailureReceiver()->all($limit);

        /** @var FailedMessage[] $messages */
        $messages = array_map(FailedMessage::fromEnvelope(...), iterator_to_array($envelopes));

        if ($latestFirst) {
            $messages = array_reverse($messages);
        }

        return $messages;
    }
}
