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
    public function getEventDispatcher(): EventDispatcherInterface;

    public function getEntityManager(): EntityManagerInterface;

    public function save(GenericEvent $before, $entity, GenericEvent $after): void;

    public function delete(GenericEvent $before, $entity, GenericEvent $after): void;
}
