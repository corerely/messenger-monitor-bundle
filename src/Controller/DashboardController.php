<?php
declare(strict_types=1);

namespace Corerely\MessengerMonitorBundle\Controller;

use Corerely\MessengerMonitorBundle\Failed\FailedMessageRepository;
use Corerely\MessengerMonitorBundle\Locator\ReceiverLocator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Routing\Attribute\Route;

abstract class DashboardController extends AbstractController
{

    protected int $failedMessagesLimit = 1000;

    public function __construct(
        #[Autowire(service: 'corerely.messenger_monitor_bundle.receiver_locator')]
        private readonly ReceiverLocator         $receiverLocator,

        #[Autowire(service: 'corerely.messenger_monitor_bundle.failed_message_repository')]
        private readonly FailedMessageRepository $failedMessageRepository,
    ) {
    }

    #[Route('/', name: 'corerely_messenger_monitor_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        $receiversInfo = [];

        foreach ($this->receiverLocator->getReceiversMapping() as $name => $receiver) {
            $receiversInfo[$name] = $receiver instanceof MessageCountAwareInterface ? $receiver->getMessageCount() : null;
        }

        $failedMessages = $this->failedMessageRepository->listFailedMessages($this->failedMessagesLimit);

        return $this->render('@CorerelyMessengerMonitor/dashboard/index.html.twig', [
            'receiversInfo' => $receiversInfo,
            'failedMessages' => $failedMessages,
        ]);
    }
}
