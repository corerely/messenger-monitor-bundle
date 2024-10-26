<?php
declare(strict_types=1);

namespace Corerely\MessengerMonitorBundle\Controller;

use Corerely\MessengerMonitorBundle\Failed\FailedMessageRepository;
use Corerely\MessengerMonitorBundle\Failed\FailedMessageRetryer;
use Corerely\MessengerMonitorBundle\Locator\ReceiverLocator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Routing\Attribute\Route;

abstract class MessengerMonitorController extends AbstractController
{

    protected int $failedMessagesLimit = 1000;

    #[Route('/', name: 'corerely.messenger_monitor.dashboard', methods: ['GET'])]
    public function dashboard(
        #[Autowire(service: 'corerely.messenger_monitor_bundle.receiver_locator')]
        ReceiverLocator         $receiverLocator,

        #[Autowire(service: 'corerely.messenger_monitor_bundle.failed_message_repository')]
        FailedMessageRepository $failedMessageRepository,
    ): Response {
        $receiversInfo = [];

        foreach ($receiverLocator->getReceiversMapping() as $name => $receiver) {
            $receiversInfo[$name] = $receiver instanceof MessageCountAwareInterface ? $receiver->getMessageCount() : null;
        }

        $failedMessages = $failedMessageRepository->listFailedMessages($this->failedMessagesLimit);

        return $this->render('@CorerelyMessengerMonitor/messages_monitor/dashboard.html.twig', [
            'receiversInfo' => $receiversInfo,
            'failedMessages' => $failedMessages,
        ]);
    }

    #[Route('/failed/retry/{id}', name: 'corerely.messenger_monitor.failed_message.retry', methods: ['POST'])]
    public function retry(
        string               $id,
        RequestStack         $requestStack,

        #[Autowire(service: 'corerely.messenger_monitor_bundle.failed_message_retryer')]
        FailedMessageRetryer $failedMessageRetryer,
    ): Response {
        $sessionBag = $requestStack->getSession()->getBag('flashes');

        try {
            $failedMessageRetryer->retryFailedMessage($id);
            $sessionBag->add('corerely.messenger_monitor.success', \sprintf('Message with id "%s" correctly retried.', $id));
        } catch (\Exception $exception) {
            $sessionBag->add('corerely.messenger_monitor.error', \sprintf('Error while retrying message with id "%s": %s', $id, $exception->getMessage()));
        }


        return $this->redirectToRoute('corerely.messenger_monitor.dashboard');
    }
}
