<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Tests\Functional\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Nfq\AdminBundle\DependencyInjection\Configuration;
use Nfq\AdminBundle\DependencyInjection\NfqAdminExtension;

/**
 * Class NfqAdminExtensionTest
 * @package Nfq\AdminBundle\Tests\Functional\DependencyInjection
 */
class NfqAdminExtensionTest extends \PHPUnit_Framework_TestCase {

    public function testLoad()
    {

        $config = array();
        $container = new ContainerBuilder();

        $extension = new NfqAdminExtension();

        $extension->load($config, $container);
        $paramBag = $container->getParameterBag();
        $this->assertEquals(array(), \PHPUnit_Framework_Assert::readAttribute($paramBag, 'parameters'));
    }
}
