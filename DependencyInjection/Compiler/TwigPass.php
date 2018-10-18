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


use Nfq\AdminBundle\Helper\ContextHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TwigPass
 * @package Nfq\AdminBundle\DependencyInjection\Compiler
 */
class TwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!isset($bundles['TwigBundle'])) {
            return;
        }

        $twigFormResources = $container->getParameter('twig.form.resources');
        if (!\is_array($twigFormResources)) {
            $twigFormResources = [];
        }

        \array_push($twigFormResources, '@NfqAdmin/layout/form-theme.html.twig');
        $container->setParameter('twig.form.resources', $twigFormResources);

        $twigDef = $container->getDefinition('twig');
        $twigDef->addMethodCall(
            'addGlobal',
            [
                'nfq_admin_context',
                new Reference(ContextHelper::class),
            ]
        );
    }
}
