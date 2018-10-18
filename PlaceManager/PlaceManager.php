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

use Nfq\AdminBundle\PlaceManager\Repository\PlaceAwareRepositoryInterface;

/**
 * Class PlaceManager
 * @package Nfq\AdminBundle\PlaceManager
 */
abstract class PlaceManager implements PlaceManagerInterface
{
    /**
     * @var array
     */
    protected $places = [];

    /**
     * {@inheritdoc}
     */
    abstract public function getItemsInPlace($placeId, $locale);

    abstract protected function getPlaceAwareRepository(): PlaceAwareRepositoryInterface;

    /**
     * @param mixed $item
     * @param string $key
     */
    public function formatPlaceChoice(&$item, $key)
    {
        $item = sprintf('%s (%d/%d)', $item['title'], $this->getUsedPlaceSlots($key), $item['limit']);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceChoices()
    {
        $places = $this->getPlaces();
        array_walk($places, [$this, 'formatPlaceChoice']);

        return $places;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlaces(array $places)
    {
        $this->places = [];

        foreach ($places as $placeId => $placeData) {
            $this->places[$placeId] = $placeData;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function placeExists($placeId)
    {
        return $placeId && isset($this->places[$placeId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlace($placeId, $check = true)
    {
        if ($check && !$this->placeExists($placeId)) {
            return [];
        }

        return $this->places[$placeId];
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceLimit($placeId)
    {
        $place = $this->getPlace($placeId, true);

        return ($place) ? $place['limit'] : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsedPlaceSlots($placeId)
    {
        return $this->getPlaceAwareRepository()->getUsedPlaceSlots($placeId);
    }

    /**
     * Checks if given place has empty slots available.
     *
     * @param string $placeId
     * @return bool|int
     */
    public function hasEmptySlots($placeId)
    {
        if ($this->placeExists($placeId)) {
            $count = $this->getUsedPlaceSlots($placeId);

            return ($count <= $this->getPlace($placeId, false)['limit']);
        }

        return -1;
    }
}
