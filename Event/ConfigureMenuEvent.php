<?php declare(strict_types=1);

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
    public const SIDE_MENU = 'nfq_admin.side_menu_configure';

    /**
     * Header menu configuration event name.
     */
    public const HEADER_MENU = 'nfq_admin.header_menu_configure';

    /** @var FactoryInterface */
    private $factory;

    /** @var ItemInterface */
    private $menu;

    /** @var Request|null */
    private $request;

    public function __construct(FactoryInterface $factory, ItemInterface $menu, ?Request $request)
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->request = $request;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
