<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Decorated;

/**
 * Interface DecoratedInterface
 * @package Nfq\AdminBundle\Decorated
 */
interface DecoratedInterface
{
    /**
     * Executes methods of decorated service
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args);

    /**
     * Sets decorated service
     *
     * @param $decorated
     * @return mixed
     */
    public function setDecoratedService($decorated);

    /**
     * Returns decorated service.
     *
     * @return object
     */
    public function getDecoratedService();
}
