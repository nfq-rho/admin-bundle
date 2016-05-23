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

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ConfigureMenuEvent
 * @package Nfq\AdminBundle\Event
 */
class ConfigureMenuEvent extends Event
{
    /**
     * Side menu configuration event name.
     */
    const SIDE_MENU = 'nfq_admin.side_menu_configure';
    /**
     * Header menu configuration event name.
     */
    const HEADER_MENU = 'nfq_admin.header_menu_configure';

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ItemInterface
     */
    private $menu;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param FactoryInterface $factory
     * @param ItemInterface    $menu
     * @param Request          $request
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, Request $request)
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->request = $request;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
