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

use Nfq\AdminBundle\Event\ConfigureMenuEvent;

/**
 * Interface AdminMenuListenerInterface
 * @package Nfq\AdminBundle\Menu
 */
interface AdminMenuListenerInterface
{
    public function onMenuConfigure(ConfigureMenuEvent $event): void;
}
