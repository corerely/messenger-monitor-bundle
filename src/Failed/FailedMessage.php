<?php
declare(strict_types=1);

namespace Corerely\MessengerMonitorBundle\Failed;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

final readonly class FailedMessage
{

    public function __construct(
        public int|string|null     $id,
        public ?string             $class,
        public ?\DateTimeInterface $failedAt,
        public array               $objectVars,
        public array               $errors,
    ) {
    }

    public function getLastError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    public function restOfErrors(): array
    {
        return array_slice($this->errors, 1);
    }

    public static function fromEnvelope(Envelope $envelope): self
    {
        /** @var ErrorDetailsStamp[] $errors */
        $errors = array_reverse($envelope->all(ErrorDetailsStamp::class));

        /** @var TransportMessageIdStamp|null $transportMessageStamp */
        $transportMessageStamp = $envelope->last(TransportMessageIdStamp::class);
        /** @var RedeliveryStamp|null $redeliveryStamp */
        $redeliveryStamp = $envelope->last(RedeliveryStamp::class);

        $message = $envelope->getMessage();

        return new self(
            $transportMessageStamp?->getId(),
            $message::class,
            $redeliveryStamp?->getRedeliveredAt(),
            get_object_vars($message),
            array_map(
                static fn(ErrorDetailsStamp $error) => $error->getExceptionMessage(),
                $errors,
            ),
        );
    }
}
