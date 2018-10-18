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

use Nfq\AdminBundle\Service\Generic\Actions\GenericActions;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AdminManagerPass
 * @package Nfq\AdminBundle\DependencyInjection\Compiler
 */
class AdminManagerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $searchManagerMap = [];
        //Builds search Services
        foreach ($container->findTaggedServiceIds('nfq_admin.search') as $id => $attributes) {
            $connection = 'default';

            if (isset($attributes[0]['connection'])) {
                $connection = $attributes[0]['connection'];
            }

            if (!isset($attributes[0]['manager'])) {
                throw new \InvalidArgumentException("Search service `{$id}` is missing attribute `manager`");
            }

            $emId = sprintf('doctrine.orm.%s_entity_manager', $connection);

            $def = $container->getDefinition($id);
            $def->addMethodCall('setEntityManager', [new Reference($emId)]);

            if ($attributes[0]['manager'] !== false) {
                $searchManagerMap[$attributes[0]['manager']] = $id;
            }
        }

        //Set search and actions
        foreach ($container->findTaggedServiceIds('nfq_admin.manager') as $id => $attributes) {
            $def = $container->getDefinition($id);

            if (isset($searchManagerMap[$id])) {
                $def->addMethodCall('setSearch', [new Reference($searchManagerMap[$id])]);
            }

            $def->addMethodCall('setActions', [new Reference(GenericActions::class)]);
        }
    }
}
