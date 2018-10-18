<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\PlaceManager\Repository;

/**
 * Interface PlaceAwareRepositoryInterface
 * @package Nfq\AdminBundle\PlaceManager\Repository
 */
interface PlaceAwareRepositoryInterface
{
    /**
     * @param string $placeId
     * @return int
     */
    public function getUsedPlaceSlots(string $placeId): int;
}
