<?php

namespace App;

use App\Definition\TwigDecorator;
use App\Decorator\FilterManagerDecorator;
use App\Decorator\EntityUserProviderDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('twig');
        $definition->setClass(TwigDecorator::class);
        $definition->addMethodCall('setRequestStack', [new Reference('request_stack')]);
    }
}
