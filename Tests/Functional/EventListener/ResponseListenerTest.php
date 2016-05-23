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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Nfq\AdminBundle\EventListener\ResponseListener;

/**
 * Class ResponseListenerTest
 * @package Nfq\AdminBundle\Tests\Functional\EventListener
 */
class ResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getTestOnKernelResponseData()
    {
        $out = array();

        // case #0: Check if return is Modal
        $isModal = true;
        $expected = array(
            'instance' => 'Symfony\Component\HttpFoundation\JsonResponse',
            'content' => '{"status":"redirect","content":"redirectUri"}'
        );
        $out[] = array($isModal, $expected);

        // case #1: check if its not Modal
        $isModal = false;
        $expected = 'Symfony\Component\HttpFoundation\RedirectResponse';
        $out[] = array($isModal, $expected);
        return $out;
    }

    /**
     * @dataProvider getTestOnKernelResponseData
     * @param boolean $isModal
     * @param $expected
     */
    public function testOnKernelResponse($isModal, $expected)
    {
        /** @var \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event */
        $event = $this->getFilterResponseEvent($isModal);
        $responseListener = $this->getResponseListener($event);
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
            'instance' => 'Symfony\Component\HttpFoundation\JsonResponse',
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

        $this->assertSame($expect, ResponseListener::getSubscribedEvents());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRedirectResponseMock()
    {
        $redirectResponse = $this->getMockBuilder('\Symfony\Component\HttpFoundation\RedirectResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $redirectResponse->method('getTargetUrl')->willReturn('redirectUri');
        return $redirectResponse;
    }


    /**
     * @param bool $isModal
     * @return array
     */
    private function getResponseForControllerResultEvent($isModal = false)
    {
        list($redirectResponse, $kernel, $request) = $this->getEventConfiguration($isModal);
        $request->attributes->set('_template', 'template');
        $request->headers->set('x-thermomix-header', 'JsonResponse');
        $event = new GetResponseForControllerResultEvent(
            $kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $redirectResponse
        );
        $event->setControllerResult(array());
        return $event;
    }


    /**
     * @param bool $isModal
     * @return array
     */
    private function getFilterResponseEvent($isModal = false)
    {
        list($redirectResponse, $kernel, $request) = $this->getEventConfiguration($isModal);
        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $redirectResponse);
        return $event;
    }

    /**
     * @return \Nfq\AdminBundle\EventListener\ResponseListener
     */
    private function getResponseListener()
    {
        $responseListener = new ResponseListener();
        return $responseListener;
    }

    /**
     * @param $isModal
     * @return array
     */
    private function getEventConfiguration($isModal)
    {
        $redirectResponse = $this->getRedirectResponseMock();
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');

        $request = new Request();
        $request->query->set('isModal', $isModal);
        return array($redirectResponse, $kernel, $request);
    }
}
