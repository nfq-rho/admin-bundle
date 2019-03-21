<?php declare(strict_types=1);

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

    public function __call(string $method, array $args)
    {
        $methodAvailable = [$this->decorated, $method];
        if (is_callable($methodAvailable)) {
            return call_user_func_array($methodAvailable, $args);
        }
    }

    public function getDecoratedService()
    {
        return $this->decorated;
    }

    public function setDecoratedService($decorated): void
    {
        $this->decorated = $decorated;
    }
}
