<?php

namespace App;

use Override;
use Doctrine\ORM\Events;
use App\Definition\TwigDecorator;
use App\Decorator\FilterManagerDecorator;
use App\Decorator\EntityUserProviderDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use App\Autowire\Doctrine\{OnClearEventListener,
    PostFlushEventListenerInterface,
    PreUpdateEventListenerInterface,
    PostUpdateEventListenerInterface,
    PostPersistEventListenerInterface};

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    #[Override]
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('twig');
        $definition->setClass(TwigDecorator::class);
        $definition->addMethodCall('setRequestStack', [new Reference('request_stack')]);
    }

    #[Override]
    protected function build(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(OnClearEventListener::class)->addTag('doctrine.event_listener', ['event' => Events::onClear, 'priority' => 100]);
        $container->registerForAutoconfiguration(PreUpdateEventListenerInterface::class)->addTag('doctrine.event_listener', ['event' => Events::preUpdate, 'method' => 'preUpdate', 'priority' => 100, 'connection' => 'default']);
        $container->registerForAutoconfiguration(PostUpdateEventListenerInterface::class)->addTag('doctrine.event_listener', ['event' => Events::postUpdate, 'method' => 'postUpdate', 'priority' => 100, 'connection' => 'default']);
        $container->registerForAutoconfiguration(PostPersistEventListenerInterface::class)->addTag('doctrine.event_listener', ['event' => Events::postPersist, 'method' => 'postPersist', 'priority' => 100, 'connection' => 'default']);
        $container->registerForAutoconfiguration(PostFlushEventListenerInterface::class)->addTag('doctrine.event_listener', ['event' => Events::postFlush, 'method' => 'postFlush', 'priority' => 100, 'connection' => 'default']);
    }
}
