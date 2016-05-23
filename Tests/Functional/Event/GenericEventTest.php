<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Tests\Functional\Event;

use Nfq\AdminBundle\Event\GenericEvent;
use Nfq\AdminBundle\Notices;

/**
 * Class GenericEventTest
 * @package Nfq\AdminBundle\Tests\Functional\Event
 */
class GenericEventTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $entity = new \stdClass();
        $eventName = 'name';
        $message = 'message';
        $expectMessage = array(Notices::NOTICE_SUCCESS => array($message));
        $event = new GenericEvent($entity, $eventName, $message);
        $this->assertEquals($entity, $event->getEntity());
        $this->assertEquals($expectMessage, $event->getResponseMessage());
        $this->assertEquals($eventName, $event->getEventName());
        $this->assertEquals($entity, $event->getOldEntity());
        $this->assertEquals(true, $event->isOk());
    }
}
