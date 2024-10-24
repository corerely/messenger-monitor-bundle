<?php
declare(strict_types=1);

namespace Corerely\MessengerMonitorBundle;

use Corerely\MessengerMonitorBundle\DependencyInjection\FailureReceiverProviderPass;
use Corerely\MessengerMonitorBundle\DependencyInjection\ReceiverLocatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CorerelyMessengerMonitorBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ReceiverLocatorPass());
        $container->addCompilerPass(new FailureReceiverProviderPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
