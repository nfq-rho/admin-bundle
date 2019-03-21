<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\DependencyInjection\Compiler;

use Nfq\AdminBundle\Paginator\Paginator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PaginatorPass
 * @package Nfq\AdminBundle\DependencyInjection\Compiler
 */
class PaginatorPass implements CompilerPassInterface
{
    private const SERVICE_NAME_KNP_PAGINATOR = 'knp_paginator';
    private const SERVICE_NAME_PAGERFANTA_PAGINATOR = 'pagerfanta_paginator';

    public function process(ContainerBuilder $container): void
    {
        $default = false;

        if ($this->hasKnpPaginator($container)) {
            $id = self::SERVICE_NAME_KNP_PAGINATOR;
        } elseif ($this->hasPagerfantaPaginator()) {
            $id = self::SERVICE_NAME_PAGERFANTA_PAGINATOR;

            $definition = new Definition('Pagerfanta\\Pagerfanta');
            $definition->setLazy(true);

            $container->setDefinition($id, $definition);
        } else {
            /**
             * @TODO: use id from bundle config
             */
            $id = Paginator::class;
            $default = true;
        }

//        if (!$default) {
            $container->setAlias(Paginator::class . '.default', $id);
//        }

        $this->createPaginatorDefinition($container, $default);
    }

    private function hasKnpPaginator(ContainerBuilder $container): bool
    {
        return $container->hasDefinition(self::SERVICE_NAME_KNP_PAGINATOR);
    }

    private function proxyManagerExists(): bool
    {
        return (class_exists('ProxyManager\\Configuration')
            && class_exists('Symfony\\Bridge\\ProxyManage\r\\LazyProxy\\PhpDumper\\ProxyDumper'));
    }

    private function hasPagerfantaPaginator(): bool
    {
        //ProxyManager is required so that we could be able to initialize Pagerfanta paginator as a service
        return ($this->proxyManagerExists() && class_exists('Pagerfanta\\Pagerfanta'));
    }

    private function createPaginatorDefinition(ContainerBuilder $container, bool $isDefault): void
    {
        $definition = new Definition(Paginator::class);

        $definition
            ->addMethodCall('setMaxPerPage', [$container->getParameter('global_max_per_page')]);

        if (!$isDefault) {
            $definition
                ->setDecoratedService(Paginator::class . '.default')
                ->addMethodCall('setDecoratedService', [new Reference(Paginator::class . '.inner')]);
        }

        //Add tagged pagination adapters
        foreach ($container->findTaggedServiceIds('nfq_admin.paginator_adapter') as $id => $attributes) {
            $definition->addMethodCall('addAdapter', [new Reference($id)]);
        }

        $container->setDefinition(Paginator::class, $definition);
    }
}
