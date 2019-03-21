<?php declare(strict_types=1);

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
    /** @var array */
    protected $places = [];

    /**
     * {@inheritdoc}
     */
    abstract public function getItemsInPlace(string $placeId, string $locale, string $sortOrder = 'ASC'): array;

    abstract protected function getPlaceAwareRepository(): PlaceAwareRepositoryInterface;

    public function formatPlaceChoice(array &$item, string $key): void
    {
        $item = sprintf('%s (%d/%d)', $item['title'], $this->getUsedPlaceSlots($key), $item['limit']);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceChoices(): array
    {
        $places = $this->getPlaces();
        array_walk($places, [$this, 'formatPlaceChoice']);

        return $places;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaces(): array
    {
        return $this->places;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlaces(array $places): void
    {
        $this->places = [];

        foreach ($places as $placeId => $placeData) {
            $this->places[$placeId] = $placeData;
        }
    }

    public function placeExists(string $placeId): bool
    {
        return $placeId && isset($this->places[$placeId]);
    }

    public function getPlace(string $placeId): array
    {
        return $this->placeExists($placeId) ? $this->places[$placeId] : [];
    }

    public function getPlaceLimit(string $placeId): int
    {
        $place = $this->getPlace($placeId, true);

        return $place['limit'] ?? 0;
    }

    public function getUsedPlaceSlots(string $placeId): int
    {
        return $this->getPlaceAwareRepository()->getUsedPlaceSlots($placeId);
    }

    public function hasEmptySlots(string $placeId): bool
    {
        if ($this->placeExists($placeId)) {
            $count = $this->getUsedPlaceSlots($placeId);

            return ($count <= $this->getPlace($placeId, false)['limit']);
        }

        return false;
    }
}
