<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Helper;

class ContextHelper extends \ArrayObject
{
    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * @param string $name
     * @param $value
     * @return ContextHelper
     */
    public function setOption(string $name, $value): self
    {
        $this->offsetSet($name, $value);
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return $this->offsetExists($name);
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getOption(string $name, $default = null)
    {
        return $this->offsetExists($name) ? $this->offsetGet($name) : $default;
    }
}
