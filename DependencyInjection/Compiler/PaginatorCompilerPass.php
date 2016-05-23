<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PaginatorCompilerPass
 * @package Nfq\AdminBundle\DependencyInjection\Compiler
 */
class PaginatorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $alias = null;
        $default = false;

        if ($this->hasKnpPaginator($container)) {
            $alias = 'knp_paginator';
        } elseif ($this->hasPagerfantaPaginator()) {
            $definition = new Definition('Pagerfanta\\Pagerfanta');
            $definition->setLazy(true);

            $container->setDefinition('pagerfanta_paginator', $definition);

            $alias = 'pagerfanta_paginator';
        } else {
            /**
             * @TODO: use alias from bundle config
             */
            $alias = 'nfq_admin.paginator';
            $default = true;
        }

        if ($alias) {
            $container->setAlias('nfq_admin.paginator.default', $alias);

            $this->createPaginatorDefinition($container, $default);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @return bool
     */
    private function hasKnpPaginator(ContainerBuilder $container)
    {
        return $container->hasDefinition('knp_paginator');
    }

    /**
     * @return bool
     */
    private function proxyManagerExists()
    {
        return (class_exists('ProxyManager\Configuration')
            && class_exists('Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper'));
    }

    /**
     * @return bool
     */
    private function hasPagerfantaPaginator()
    {
        //ProxyManager is required so that we could be able to initialize Pagerfanta paginator as a service
        return ($this->proxyManagerExists() && class_exists('Pagerfanta\\Pagerfanta'));
    }

    /**
     * @param ContainerBuilder $container
     */
    private function createPaginatorDefinition(ContainerBuilder $container, $isDefault)
    {
        $definition = new Definition('Nfq\\AdminBundle\\Paginator\\Paginator');

        $definition
            ->setPublic(false)
            ->addMethodCall('setMaxPerPage', [$container->getParameter('global_max_per_page')]);

        if (!$isDefault) {
            $definition
                ->setDecoratedService('nfq_admin.paginator.default')
                ->addMethodCall('setDecoratedService', [new Reference('nfq_admin.paginator.inner')]);
        }

        //Add tagged pagination adapters
        foreach ($container->findTaggedServiceIds('nfq_paginator_adapter') as $id => $attributes) {
            $definition->addMethodCall('addAdapter', [new Reference($id)]);
        }

        $container->setDefinition('nfq_admin.paginator', $definition);
    }
}
