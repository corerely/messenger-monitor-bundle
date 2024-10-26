<?php
declare(strict_types=1);

namespace Corerely\MessengerMonitorBundle\Failed;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Transport\Receiver\SingleMessageReceiver;
use Symfony\Component\Messenger\Worker;

final readonly class FailedMessageRetryer
{
    public function __construct(
        private FailureReceiverProvider  $failureReceiverProvider,
        private MessageBusInterface      $messageBus,
        private EventDispatcherInterface $eventDispatcher,
        private ?LoggerInterface         $logger,
    ) {
    }

    public function retry(int|string $id): void
    {
        $failureReceiver = $this->failureReceiverProvider->getFailureReceiver();
        $failureReceiverName = $this->failureReceiverProvider->getFailureReceiverName();

        $envelope = $failureReceiver->find($id);
        if (null === $envelope) {
            throw new \RuntimeException(\sprintf('The message "%s" was not found.', $id));
        }

        $this->eventDispatcher->addSubscriber($subscriber = new StopWorkerOnMessageLimitListener(1));

        $failedRetryError = null;
        $listener = static function (WorkerMessageFailedEvent $messageReceivedEvent) use (&$failedRetryError): void {
            $errorStamp = $messageReceivedEvent->getEnvelope()->last(ErrorDetailsStamp::class);
            $failedRetryError = $errorStamp?->getExceptionMessage();
        };
        $this->eventDispatcher->addListener(WorkerMessageFailedEvent::class, $listener);

        $singleReceiver = new SingleMessageReceiver($failureReceiver, $envelope);

        $worker = new Worker(
            [$failureReceiverName => $singleReceiver],
            $this->messageBus,
            $this->eventDispatcher,
            $this->logger,
        );
        $worker->run();

        $this->eventDispatcher->removeSubscriber($subscriber);
        $this->eventDispatcher->removeListener(WorkerMessageFailedEvent::class, $listener);

        if (null !== $failedRetryError) {
            throw new \RuntimeException($failedRetryError);
        }
    }

    public function reject(int|string $id): void
    {
        $failureReceiver = $this->failureReceiverProvider->getFailureReceiver();

        $envelope = $failureReceiver->find($id);

        if (null === $envelope) {
            throw new \RuntimeException(\sprintf('The message with id "%s" was not found.', $id));
        }

        $failureReceiver->reject($envelope);
    }
}
