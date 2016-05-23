<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\EventListener;

use Nfq\AdminBundle\Event\GenericEvent;
use Nfq\AdminBundle\Service\Notice;

/**
 * Class NoticeListener
 * @package Nfq\AdminBundle\EventListener
 */
class NoticeListener
{

    protected $notice;
    protected $field;

    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }

    public function onSuccess(GenericEvent $event)
    {
        $this->notice->addSuccess($event->getResponseMessage());
    }

    public function onDanger(GenericEvent $event)
    {
        $this->notice->addDanger($event->getResponseMessage());
    }

    public function onInfo(GenericEvent $event)
    {
        $this->notice->addInfo($event->getResponseMessage());
    }

    public function onWarning(GenericEvent $event)
    {
        $this->notice->addWarning($event->getResponseMessage());
    }

    public function onMessage(GenericEvent $event)
    {
        $messageList = $event->getResponseMessage();
        foreach ($messageList as $type => $messages) {
            foreach ($messages as $message) {
                if (empty($message)) {
                    continue;
                }
                $this->notice->add($message, $type);
            }
        }
    }
}
