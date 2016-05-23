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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Nfq\AdminBundle\DependencyInjection\Configuration;

/**
 * Class ConfigurationTest
 * @package Nfq\AdminBundle\Tests\Functional\DependencyInjection
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('nfq_admin');

        $config = new Configuration();
        $this->assertEquals($treeBuilder, $config->getConfigTreeBuilder());
    }
}
