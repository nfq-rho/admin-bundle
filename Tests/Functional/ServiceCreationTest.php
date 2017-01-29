<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Tests\Functional;

use Nfq\AdminBundle\EventListener\LocaleListener;
use Nfq\AdminBundle\EventListener\ModalResponseListener;
use Nfq\AdminBundle\Form\Extension\TinyMCEExtension;
use Nfq\AdminBundle\Service\FormManager;
use Nfq\AdminBundle\Service\Generic\Actions\GenericActions;
use Nfq\AdminBundle\Service\Notice;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ServiceCreationTest
 * @package Nfq\AdminBundle\Tests\Functional
 */
class ServiceCreationTest extends WebTestCase
{
    /**
     * Tests if container is returned.
     */
    public function testGetContainer()
    {
        $container = self::createClient()->getKernel()->getContainer();
        $this->assertNotNull($container);
    }

    /**
     * Tests if service are created correctly.
     *
     * @param string $service
     * @param string $instance
     *
     * @dataProvider getTestServiceCreateData
     */
    public function testServiceCreate($serviceId, $instance)
    {
        $container = self::createClient()->getKernel()->getContainer();
        $this->assertTrue($container->has($serviceId), sprintf('Service `%s` was not found in container', $serviceId));

        $service = $container->get($serviceId);
        $this->assertInstanceOf($instance, $service,
            sprintf('Invalid instance `%s` for service `%s`', $instance, $serviceId));
    }

    /**
     * Data provider for testServiceCreate().
     * 
     * @return array
     */
    public function getTestServiceCreateData()
    {
        return [
            [
                'nfq_admin.form_service',
                FormManager::class,
            ],
            [
                'nfq_admin.notice_service',
                Notice::class,
            ],
            [
                'nfq_admin.locale_listener',
                LocaleListener::class,
            ],
            [
                'nfq_admin.modal_response_listener',
                ModalResponseListener::class,
            ],
            [
                'nfq_admin.generic_actions_service',
                GenericActions::class,
            ],
            [
                'nfq_admin.form_ext.tiny_mce',
                TinyMCEExtension::class,
            ],
        ];
    }
}
