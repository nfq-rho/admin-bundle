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

use Nfq\AdminBundle\DependencyInjection\Compiler\AdminManagerPass;
use Nfq\AdminBundle\DependencyInjection\Compiler\AdminMenuPass;
use Nfq\AdminBundle\DependencyInjection\Compiler\PaginatorPass;
use Nfq\AdminBundle\DependencyInjection\Compiler\TwigPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NfqAdminBundle
 * @package Nfq\AdminBundle
 */
class NfqAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AdminMenuPass());
        $container->addCompilerPass(new PaginatorPass());
        $container->addCompilerPass(new TwigPass());
    }
}
