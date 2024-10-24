<?php

namespace Corerely\MessengerMonitorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FailureReceiverProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $failureReceiverNameDefinition = $container->getDefinition('corerely.messenger_monitor_bundle.failure_receiver_provider');
        $consumeCommandDefinition = $container->getDefinition('console.command.messenger_failed_messages_show');

        $failureReceiverNameDefinition->replaceArgument(1, $consumeCommandDefinition->getArgument(0));
    }
}
