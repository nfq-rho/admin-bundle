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

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Nfq\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MenuBuilderSubscriber
 * @package Nfq\AdminBundle\EventListener
 */
class MenuBuilderSubscriber implements EventSubscriberInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::HEADER_MENU => 'addHeaderMenuNode',
            ConfigureMenuEvent::SIDE_MENU => 'addSideMenuNode',
        ];
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function addSideMenuNode(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        $this->setFactory($event);

        $menu->setUri($event->getRequest()->getRequestUri());
        $menu->addChild($this->getDashboardNode());
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function addHeaderMenuNode(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        $this->setFactory($event);
        $menu->setUri($event->getRequest()->getRequestUri());

        $menu->addChild($this->getDividerNode());
        $menu->addChild($this->getLogoutNode());
    }

    /**
     * @return ItemInterface
     */
    private function getDividerNode()
    {
        return $this
            ->factory
            ->createItem('divider')
            ->setAttribute('class', 'divider');
    }

    /**
     * @return ItemInterface
     */
    private function getLogoutNode()
    {
        return $this
            ->factory
            ->createItem('admin.header_menu.logout', ['route' => 'admin_logout'])
            ->setExtras(
                [
                    'orderNumber' => 9999,
                    'label-icon' => 'fa fa-sign-out',
                    'translation_domain' => 'adminInterface',
                ]
            )
            ->setLinkAttribute('class', 'logout-link');
    }

    /**
     * @return ItemInterface
     */
    private function getDashboardNode()
    {
        return $this
            ->factory
            ->createItem('admin.side_menu.dashboard', ['route' => 'admin_dashboard'])
            ->setExtras(
                [
                    'orderNumber' => 10,
                    'label-icon' => 'fa fa-home',
                    'translation_domain' => 'adminInterface',
                ]
            );
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    private function setFactory(ConfigureMenuEvent $event)
    {
        $this->factory = $event->getFactory();
    }
}
