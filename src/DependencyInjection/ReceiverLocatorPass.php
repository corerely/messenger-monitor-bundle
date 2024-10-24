<?php

namespace Corerely\MessengerMonitorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ReceiverLocatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $receiverLocatorDefinition = $container->getDefinition('corerely.messenger_monitor_bundle.receiver_locator');
        $consumeCommandDefinition = $container->getDefinition('console.command.messenger_consume_messages');

        $names = $consumeCommandDefinition->getArgument(4);
        $receiverLocatorDefinition->replaceArgument(1, $names);
    }
}
