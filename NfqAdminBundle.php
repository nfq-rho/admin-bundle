<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle;

use Nfq\AdminBundle\DependencyInjection\Compiler\AdminManagersCompilerPass;
use Nfq\AdminBundle\DependencyInjection\Compiler\AdminMenuCompilerPass;
use Nfq\AdminBundle\DependencyInjection\Compiler\PaginatorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NfqAdminBundle
 * @package Nfq\AdminBundle
 */
class NfqAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AdminManagersCompilerPass());
        $container->addCompilerPass(new PaginatorCompilerPass());
        $container->addCompilerPass(new AdminMenuCompilerPass());
    }
}
