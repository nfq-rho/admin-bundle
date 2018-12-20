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
     * @return mixed
     */
    public function __call(string $method, array $args);

    /**
     * @param $decorated
     */
    public function setDecoratedService($decorated): void;

    /**
     * @return object
     */
    public function getDecoratedService();
}
