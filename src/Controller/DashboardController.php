<?php

namespace Corerely\MessengerMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

abstract class DashboardController extends AbstractController
{
    #[Route('/', name: 'corerely_messenger_monitor_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@CorerelyMessengerMonitor/dashboard/index.html.twig');
    }
}
