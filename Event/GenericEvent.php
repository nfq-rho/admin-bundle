<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Event;

use Nfq\AdminBundle\Service\Notice;
use Symfony\Component\EventDispatcher\Event;
use Nfq\AdminBundle\Notices;

/**
 * Class GenericEvent
 * @package Nfq\AdminBundle\Event
 */
class GenericEvent extends Event
{
    protected $entity;

    protected $oldEntity;

    protected $eventName;

    protected $ok;

    protected $message = array();

    /**
     * @param $entity
     * @param $eventName
     * @param null $message
     */
    public function __construct($entity, $eventName, $message = null)
    {
        $this->entity = $entity;
        $this->oldEntity = clone $entity;
        $this->eventName = $eventName;
        $this->ok = true;
        if ($message) {
            $this->addMessage($message);
        }
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return (boolean) $this->ok;
    }

    /**
     * @return array
     */
    public function getResponseMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getOldEntity()
    {
        return $this->oldEntity;
    }

    public function addMessage(string $message, string $type = Notice::NOTICE_SUCCESS): void
    {
        $this->message[$type][] = $message;
    }
}
