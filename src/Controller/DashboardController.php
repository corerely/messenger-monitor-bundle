<?php

namespace Corerely\MessengerMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\MessengerMonitorBundle\FailedMessage\FailedMessageRepository;
use SymfonyCasts\MessengerMonitorBundle\FailureReceiver\FailureReceiverProvider;

abstract class DashboardController extends AbstractController
{
    #[Route('/', name: 'corerely_messenger_monitor_dashboard', methods: ['GET'])]
    public function index(
        FailedMessageRepository $failedMessageRepository,
    ): Response
    {

        dd($failedMessageRepository);
        dd('hit');
        return $this->render('@CorerelyMessengerMonitor/dashboard/index.html.twig');
    }
}
