<?php
declare(strict_types=1);

use Corerely\MessengerMonitorBundle\Failed\FailedMessageRepository;
use Corerely\MessengerMonitorBundle\Failed\FailureReceiverProvider;
use Corerely\MessengerMonitorBundle\Locator\ReceiverLocator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('corerely.messenger_monitor_bundle.receiver_locator', ReceiverLocator::class)
        ->arg(0, new Reference('messenger.receiver_locator'))
        ->arg(1, null)
    ;

    $services->set('corerely.messenger_monitor_bundle.failure_receiver_provider', FailureReceiverProvider::class)
        ->arg(0, new Reference('corerely.messenger_monitor_bundle.receiver_locator'))
        ->arg(1, null)
    ;

    $services->set('corerely.messenger_monitor_bundle.failed_message_repository', FailedMessageRepository::class)
        ->arg(0, new Reference('corerely.messenger_monitor_bundle.failure_receiver_provider'))
    ;
};
