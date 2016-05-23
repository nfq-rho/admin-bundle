<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service\Generic\Actions;

use Doctrine\ORM\EntityManager;
use Nfq\AdminBundle\Event\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GenericActions
 * @package Nfq\AdminBundle\Service\Generic\Actions
 */
class GenericActions implements GenericActionsInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EntityManager $em
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @inheritdoc
     */
    public function save(GenericEvent $before, $entity, GenericEvent $after)
    {
        $this->dispatcher->dispatch($before->getEventName(), $before);
        if ($before->isOk()) {
            $this->em->persist($entity);
            $this->em->flush($entity);
        }
        $this->dispatcher->dispatch($after->getEventName(), $after);
    }

    /**
     * @inheritdoc
     */
    public function delete(GenericEvent $before, $entity, GenericEvent $after)
    {
        $this->dispatcher->dispatch($before->getEventName(), $before);
        if ($before->isOk()) {
            $this->em->remove($entity);
            $this->em->flush($entity);
        }
        $this->dispatcher->dispatch($after->getEventName(), $after);
    }
}
