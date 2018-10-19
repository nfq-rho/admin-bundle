<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\DependencyInjection;

use Nfq\AdminBundle\Helper\ContextHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NfqAdminExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $contextHelperDef = $container->getDefinition(ContextHelper::class);

        $this->configureMenu($contextHelperDef, $container);
        $this->configureSidebar($contextHelperDef, $container);
        $this->configurePaging($contextHelperDef, $container);

        $contextHelperDef->addMethodCall('setOption', ['default_avatar', 'bundles/nfqadmin/images/default_avatar.png']);
    }

    private function configureMenu(Definition $definition, ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        $knpMenu['enable'] = isset($bundles['KnpMenuBundle']);
        $knpMenu['breadcrumb_menu'] = false;
        $knpMenu['control_sidebar'] = false;

        $definition->addMethodCall('setOption', ['knp_menu', $knpMenu]);
    }

    private function configureSidebar(Definition $definition, ContainerBuilder $container): void
    {
        $definition->addMethodCall('setOption', ['control_sidebar', false]);
    }

    private function configurePaging(Definition $definition, ContainerBuilder $container): void
    {
        $maxPerPage = $container->getParameter('global_max_per_page');
        $definition->addMethodCall('setOption', ['default_max_per_page', $maxPerPage]);
    }
}
