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

use Nfq\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

/**
 * Class AdminMenuPass
 * @package Nfq\AdminBundle\DependencyInjection\Compiler
 */
class AdminMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        try {
            $config = $container->getParameter('nfq_admin.menu_security');
        } catch (ParameterNotFoundException $e) {
            return;
        }

        foreach ($this->getKernelEventListeners($container) as $id => $service) {
            if ($this->isAdminMenuEvent($service)) {
                $this->addGrantedRoles($container, $id, $config);
            }
        }
    }

    private function addGrantedRoles(ContainerBuilder $container, string $id, array $config): void
    {
        $bundle = $this->buildBundleNameFromNamespace($id);

        if ($this->isBundleConfigDefined($bundle, $config)) {
            $definition = $container->findDefinition($id);
            $definition->addMethodCall('setGrantedRoles', [$config[$bundle]]);
        }
    }

    private function buildBundleNameFromNamespace(string $id): string
    {
        if (false === \strpos($id, '\\')) {
            throw new \InvalidArgumentException('Can not resolve bundle name from %s . When defining your listener, specify FQCN instead');
        }

        [$vendorPart, $bundlePart] = explode('\\', $id);

        return $vendorPart . $bundlePart;
    }

    private function isAdminMenuEvent(array $service): bool
    {
        return \in_array($service[0]['event'], [ConfigureMenuEvent::HEADER_MENU, ConfigureMenuEvent::SIDE_MENU]);
    }

    private function getKernelEventListeners(ContainerBuilder $container): array
    {
        return $container->findTaggedServiceIds('kernel.event_listener');
    }

    private function isBundleConfigDefined(string $bundleNamespace, array $config): bool
    {
        return \array_key_exists($bundleNamespace, $config);
    }
}
