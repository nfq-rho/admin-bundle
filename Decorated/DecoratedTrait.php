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
 * Class DecoratedTrait
 * @package Nfq\AdminBundle\Decorated
 */
trait DecoratedTrait
{
    private $decorated;

    /**
     * @inheritdoc
     */
    public function __call($method, $args)
    {
        $methodAvailable = [$this->decorated, $method];
        if (is_callable($methodAvailable)) {
            return call_user_func_array($methodAvailable, $args);
        }
    }

    /**
     * @inheritdoc
     */
    public function getDecoratedService()
    {
        return $this->decorated;
    }

    /**
     * @inheritdoc
     */
    public function setDecoratedService($decorated)
    {
        $this->decorated = $decorated;
    }
}
