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

use Doctrine\ORM\EntityManagerInterface;
use Nfq\AdminBundle\Event\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GenericActions
 * @package Nfq\AdminBundle\Service\Generic\Actions
 */
class GenericActions implements GenericActionsInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var EventDispatcherInterface
     */
    protected $ed;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed)
    {
        $this->em = $em;
        $this->ed = $ed;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->ed;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @inheritdoc
     */
    public function save(GenericEvent $before, $entity, GenericEvent $after): void
    {
        $this->ed->dispatch($before->getEventName(), $before);
        if ($before->isOk()) {
            $this->em->persist($entity);
            $this->em->flush($entity);
        }
        $this->ed->dispatch($after->getEventName(), $after);
    }

    /**
     * @inheritdoc
     */
    public function delete(GenericEvent $before, $entity, GenericEvent $after): void
    {
        $this->ed->dispatch($before->getEventName(), $before);
        if ($before->isOk()) {
            $this->em->remove($entity);
            $this->em->flush($entity);
        }
        $this->ed->dispatch($after->getEventName(), $after);
    }
}
