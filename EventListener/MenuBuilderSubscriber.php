<?php declare(strict_types=1);

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
    /** @var FactoryInterface */
    private $factory;

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::HEADER_MENU => 'addHeaderMenuNode',
            ConfigureMenuEvent::SIDE_MENU => 'addSideMenuNode',
        ];
    }

    public function addSideMenuNode(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $this->setFactory($event);

        $menu->setUri($event->getRequest()->getRequestUri());
        $menu->addChild($this->getDashboardNode());
    }

    public function addHeaderMenuNode(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $this->setFactory($event);
        $menu->setUri($event->getRequest()->getRequestUri());

        $menu->addChild($this->getDividerNode());
        $menu->addChild($this->getLogoutNode());
    }

    private function getDividerNode(): ItemInterface
    {
        return $this
            ->factory
            ->createItem('divider')
            ->setAttribute('class', 'divider');
    }

    private function getLogoutNode(): ItemInterface
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

    private function getDashboardNode(): ItemInterface
    {
        return $this
            ->factory
            ->createItem('admin.side_menu.dashboard', ['route' => 'admin_dashboard'])
            ->setExtras(
                [
                    'orderNumber' => 10,
                    'label-icon' => 'fa fa-home',
                    'translation_domain' => 'adminInterface',
                    'routes' => [
                        'admin_dashboard'
                    ]
                ]
            );
    }

    private function setFactory(ConfigureMenuEvent $event): void
    {
        $this->factory = $event->getFactory();
    }
}
