<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\PlaceManager;

/**
 * Interface PlaceAwareInterface
 * @package Nfq\AdminBundle\PlaceManager
 */
interface PlaceAwareInterface
{
    /**
     * Set place manager.
     *
     * @param PlaceManagerInterface $manager
     * @return void
     */
    public function setPlaceManager(PlaceManagerInterface $manager);
}
