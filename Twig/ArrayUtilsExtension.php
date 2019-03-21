<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Twig;

/**
 * Class ArrayUtilsExtension
 * @package Nfq\AdminBundle\Twig
 */
class ArrayUtilsExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('unique', [$this, 'unique']),
            new \Twig_SimpleFilter('unsetByKey', [$this, 'unsetByKey']),
            new \Twig_SimpleFilter('unsetByValue', [$this, 'unsetByValue']),
            new \Twig_SimpleFilter('reindexArray', [$this, 'reindexArray']),
        ];
    }

    public function unique(array $array): array
    {
        return array_unique($array);
    }

    /**
     * @param array $array
     * @param mixed $key
     * @return array
     */
    public function unsetByKey(array $array, $key): array
    {
        if (isset($array[$key])) {
            unset($array[$key]);
        }

        return $array;
    }

    /**
     * @param array $array
     * @param mixed $value
     * @return array
     */
    public function unsetByValue(array $array, $value)
    {
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }

        return $array;
    }

    /**
     * @param array $array
     * @return array
     */
    public function reindexArray(array $array)
    {
        return array_values($array);
    }

    public function getName(): string
    {
        return 'array_utils';
    }
}
