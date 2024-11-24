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

        if (is_array($envelopes)) {
            $envelopes = new \ArrayIterator($envelopes);
        }

        $messages = [];

        do {
            try {
                $envelope = $envelopes->current();
                $envelopes->next();

                $messages[] = FailedMessage::fromEnvelope($envelope);
            } catch (\Throwable $e) {
                $messages[] = new FailedMessage(
                    id: null,
                    class: null,
                    failedAt: null,
                    objectVars: [],
                    errors: [$e->getMessage()],
                );
            }

        } while ($envelopes->valid());

        if ($latestFirst) {
            $messages = array_reverse($messages);
        }

        return $messages;
    }
}
