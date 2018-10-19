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
 * Interface PlaceManagerInterface
 * @package Nfq\AdminBundle\PlaceManager
 */
interface PlaceManagerInterface
{
    public function getItemsInPlace(string $placeId, string $locale, string $sortOrder = 'ASC'): array;

    /**
     * Get array of places for choice input type
     *
     * @return string[]
     */
    public function getPlaceChoices(): array;

    /**
     * @return string[]
     */
    public function getPlaces(): array;

    /**
     * @param string[] $places
     */
    public function setPlaces(array $places): void;

    /**
     * Checks if given place exists.
     */
    public function placeExists(string $placeId): bool;

    /**
     * Get place.
     */
    public function getPlace(string $placeId): array;

    public function getPlaceLimit(string $placeId): int;

    public function getUsedPlaceSlots(string $placeId): int;

    public function hasEmptySlots(string $place): bool;
}
