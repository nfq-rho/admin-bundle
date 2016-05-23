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
 * Class PlaceAwareTrait
 * @package Nfq\AdminBundle\PlaceManager
 */
trait PlaceAwareTrait
{
    /**
     * @var PlaceManagerInterface
     */
    protected $placeManger;

    /**
     * @param PlaceManagerInterface $manager
     */
    public function setPlaceManager(PlaceManagerInterface $manager)
    {
        $this->placeManger = $manager;
    }
}
