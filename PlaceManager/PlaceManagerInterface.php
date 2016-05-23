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
    /**
     * @param string $placeId
     * @param string $locale
     * @return array
     */
    public function getItemsInPlace($placeId, $locale);

    /**
     * Get array of places for choice input type
     *
     * @return array
     */
    public function getPlaceChoices();

    /**
     * @return array
     */
    public function getPlaces();

    /**
     * @param array $places
     * @return void
     */
    public function setPlaces(array $places);

    /**
     * Checks if given place exists.
     *
     * @param string $placeId
     * @return bool
     */
    public function placeExists($placeId);

    /**
     * Get place.
     *
     * @param string $placeId
     * @param bool $check
     * @return array
     */
    public function getPlace($placeId, $check = true);

    /**
     * @param string $placeId
     * @return int
     */
    public function getPlaceLimit($placeId);

    /**
     * @param string $placeId
     * @return int
     */
    public function getUsedPlaceSlots($placeId);

    /**
     * @param string $place
     * @return int
     */
    public function hasEmptySlots($place);
}
