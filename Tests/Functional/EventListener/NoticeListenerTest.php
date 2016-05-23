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

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Nfq\AdminBundle\Event\GenericEvent;
use Nfq\AdminBundle\EventListener\NoticeListener;
use Nfq\AdminBundle\Service\Notice;

/**
 * Class NoticeListenerTest
 * @package Nfq\AdminBundle\Tests\Functional\EventListener
 */
class NoticeListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnSuccess()
    {
        $message = 'success_message';
        $genericEvent = $this->getGenericEvent($message);

        $listener = $this->getNoticeListener();
        $listener->onSuccess($genericEvent);
        $response = $genericEvent->getResponseMessage();

        $this->assertEquals($message, $response['success'][0]);
    }

    public function testOnDanger()
    {
        $message = 'danger_message';
        $genericEvent = $this->getGenericEvent($message);

        $listener = $this->getNoticeListener();
        $listener->onDanger($genericEvent);
        $response = $genericEvent->getResponseMessage();

        $this->assertEquals($message, $response['success'][0]);
    }

    public function testOnInfo()
    {
        $message = 'info_message';
        $genericEvent = $this->getGenericEvent($message);

        $listener = $this->getNoticeListener();
        $listener->onInfo($genericEvent);
        $response = $genericEvent->getResponseMessage();

        $this->assertEquals($message, $response['success'][0]);
    }

    public function testOnWarning()
    {
        $message = 'warning_message';
        $genericEvent = $this->getGenericEvent($message);

        $listener = $this->getNoticeListener();
        $listener->onWarning($genericEvent);
        $response = $genericEvent->getResponseMessage();

        $this->assertEquals($message, $response['success'][0]);
    }

    public function getTestOnMessageData()
    {
        $out = array();
        // case #0:

        $config = array(
            'message' => 'warning_message',
            'type' => 'warning'
        );
        $expected = array(
            'message' => 'warning_message',
        );
        $out[] = array($config, $expected);
        // case #1:
        $config = array(
            'message' => '',
            'type' => 'warning'
        );
        $expected = array(
            'message' => '',
        );
        $out[] = array($config, $expected);
        return $out;
    }

    /**
     * @dataProvider getTestOnMessageData
     */
    public function testOnMessage($config, $expected)
    {
        $genericEvent = $this->getGenericEvent();
        $genericEvent->addMessage($config['message'], $config['type']);

        $listener = $this->getNoticeListener();
        $listener->onMessage($genericEvent);
        $response = $genericEvent->getResponseMessage();

        $this->assertEquals($expected['message'], $response['warning'][0]);
    }

    /**
     * @param $message
     * @return \Nfq\AdminBundle\Event\GenericEvent
     */
    private function getGenericEvent($message = null)
    {
        $entity = new \StdClass();
        $eventName = 'user.after_save';
        $genericEvent = new GenericEvent($entity, $eventName, $message);
        return $genericEvent;
    }

    /**
     * @return NoticeListener
     */
    private function getNoticeListener()
    {
        $session = new Session(new MockFileSessionStorage());
        $notice = new Notice($session);
        $listener = new NoticeListener($notice);
        return $listener;
    }
}
