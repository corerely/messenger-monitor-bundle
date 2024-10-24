<?php
declare(strict_types=1);

namespace Corerely\MessengerMonitorBundle\Failed;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

final readonly class FailedMessage
{

    public function __construct(
        public int|string|null     $id,
        public string              $class,
        public ?\DateTimeInterface $failedAt,
        public ?string             $error,
    ) {
    }

    public static function fromEnvelope(Envelope $envelope): self
    {
        return new self(
            self::lastStamp($envelope, TransportMessageIdStamp::class)?->getId(),
            $envelope->getMessage()::class,
            self::lastStamp($envelope, RedeliveryStamp::class)?->getRedeliveredAt(),
            self::lastStamp($envelope, ErrorDetailsStamp::class)?->getExceptionMessage(),
        );
    }

    /**
     * @template T
     * @return T|null
     */
    private static function lastStamp(Envelope $envelope, string $stampClass): ?StampInterface
    {
        return $envelope->last($stampClass);
    }
}
