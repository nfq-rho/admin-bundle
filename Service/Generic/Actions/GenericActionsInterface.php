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

use Nfq\AdminBundle\Event\GenericEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface GenericActionsInterface
 * @package Nfq\AdminBundle\Service\Generic\Actions
 */
interface GenericActionsInterface
{
    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher();

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager();

    /**
     * @param GenericEvent $before
     * @param $entity
     * @param GenericEvent $after
     * @return mixed
     */
    public function save(GenericEvent $before, $entity, GenericEvent $after);

    /**
     * @param GenericEvent $before
     * @param $entity
     * @param GenericEvent $after
     * @return mixed
     */
    public function delete(GenericEvent $before, $entity, GenericEvent $after);
}
