<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Nfq\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminMenuBuilder
 * @package Nfq\AdminBundle\Menu
 */
class AdminMenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function buildSideMenu(FactoryInterface $factory): ItemInterface
    {
        $menu = $factory->createItem('side-menu');
        $menu->setChildrenAttribute('class', 'sidebar-menu tree');
        $menu->setChildrenAttribute('data-widget', 'tree');

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureMenuEvent::SIDE_MENU,
            new ConfigureMenuEvent(
                $factory,
                $menu,
                $this->getCurrentRequest()
            )
        );

        $this->orderMenuItems($menu);

        return $menu;
    }

    public function buildHeaderMenu(FactoryInterface $factory): ItemInterface
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'dropdown-menu dropdown-user');

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureMenuEvent::HEADER_MENU,
            new ConfigureMenuEvent($factory, $menu, $this->getCurrentRequest())
        );

        return $menu;
    }

    private function getCurrentRequest(): ?Request
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    private function orderMenuItems(ItemInterface $menu): void
    {
        $menuOrderArray = [];
        $addLast = [];
        $alreadyTaken = [];

        foreach ($menu->getChildren() as $key => $menuItem) {

            if ($menuItem->hasChildren()) {
                $this->orderMenuItems($menuItem);
            }

            $orderNumber = $menuItem->getExtra('orderNumber');

            if ($orderNumber != null) {
                if (!isset($menuOrderArray[$orderNumber])) {
                    $menuOrderArray[$orderNumber] = $menuItem->getName();
                } else {
                    $alreadyTaken[$orderNumber] = $menuItem->getName();
                    // $alreadyTaken[] = array('orderNumber' => $orderNumber, 'name' => $menuItem->getName());
                }
            } else {
                $addLast[] = $menuItem->getName();
            }
        }

        // sort them after first pass
        ksort($menuOrderArray);

        // handle position duplicates
        $menuOrderArray = $this->handlePositionDuplicates($alreadyTaken, $menuOrderArray);

        // sort them after second pass
        ksort($menuOrderArray);

        // add items without orderNumber to the end
        if (count($addLast)) {
            foreach ($addLast as $key => $value) {
                $menuOrderArray[] = $value;
            }
        }

        if (\count($menuOrderArray)) {
            $menu->reorderChildren($menuOrderArray);
        }
    }

    private function handlePositionDuplicates(array $alreadyTaken, array $menuOrderArray): array
    {
        if (empty($alreadyTaken)) {
            return $menuOrderArray;
        }

        foreach ($alreadyTaken as $key => $value) {
            // the ever shifting target
            $keysArray = array_keys($menuOrderArray);

            $position = array_search($key, $keysArray);

            if ($position === false) {
                continue;
            }

            $menuOrderArray = array_merge(
                array_slice($menuOrderArray, 0, $position),
                [$value],
                array_slice($menuOrderArray, $position)
            );
        }

        return $menuOrderArray;
    }
}
