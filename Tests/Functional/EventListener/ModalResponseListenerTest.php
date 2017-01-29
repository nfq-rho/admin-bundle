<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Tests\Functional\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Nfq\AdminBundle\EventListener\ModalResponseListener;

/**
 * Class ModalResponseListenerTest
 * @package Nfq\AdminBundle\Tests\Functional\EventListener
 */
class ModalResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getTestOnKernelResponseData()
    {
        $out = [];

        // case #0: Check if return is Modal
        $isModal = true;
        $expected = [
            'instance' => JsonResponse::class,
            'content' => '{"status":"redirect","content":"redirectUri"}'
        ];
        $out[] = [$isModal, $expected];

        // case #1: check if its not Modal
        $isModal = false;
        $expected = RedirectResponse::class;
        $out[] = [$isModal, $expected];

        return $out;
    }

    /**
     * @dataProvider getTestOnKernelResponseData
     * @param boolean $isModal
     * @param $expected
     */
    public function testOnKernelResponse($isModal, $expected)
    {
        $event = $this->getFilterResponseEvent($isModal);
        $responseListener = $this->getResponseListener();

        $responseListener->onKernelResponse($event);

        if (is_array($expected)) {
            $this->assertInstanceOf($expected['instance'], $event->getResponse());
            $this->assertEquals($expected['content'], $event->getResponse()->getContent());
        } else {
            $this->assertInstanceOf($expected, $event->getResponse());
        }
    }


    /**
     * @return array
     */
    public function getTestOnKernelViewData()
    {
        $out = array();
        // case #0: Check if return is Modal
        $isModal = true;
        $expected = array(
            'instance' => JsonResponse::class,
            'content' => '{"status":"error","content":["content"]}'
        );
        $out[] = array($isModal, $expected);
        // case #1: check if its not Modal
        $isModal = false;
        $expected = null;
        $out[] = array($isModal, $expected);
        return $out;
    }

    public function testGetSubscribedEvents()
    {
        $expect = [
            KernelEvents::RESPONSE => [['onKernelResponse', 10]],
        ];

        $this->assertSame($expect, ModalResponseListener::getSubscribedEvents());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRedirectResponseMock()
    {
        $redirectResponse = $this->getMockBuilder(RedirectResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redirectResponse->method('getTargetUrl')->willReturn('redirectUri');

        return $redirectResponse;
    }

    /**
     * @param bool $isModal
     * @return FilterResponseEvent
     */
    private function getFilterResponseEvent($isModal = false)
    {
        list($kernel, $request, $redirectResponse) = $this->getEventConfiguration($isModal);

        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $redirectResponse);

        return $event;
    }

    /**
     * @return ModalResponseListener
     */
    private function getResponseListener()
    {
        $responseListener = new ModalResponseListener();
        return $responseListener;
    }

    /**
     * @param bool $isModal
     * @return array
     */
    private function getEventConfiguration($isModal)
    {
        $redirectResponse = $this->getRedirectResponseMock();
        $kernel = $this->createMock(HttpKernelInterface::class);

        $request = new Request(['isModal' => (int)$isModal]);

        return [$kernel, $request, $redirectResponse];
    }
}
