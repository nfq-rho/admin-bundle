<?php

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
 * Class TestExtension
 * @package Nfq\AdminBundle\Twig
 */
class TestExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('isActiveLocale', [$this, 'isActiveLocale']),
        ];
    }

    /**
     * @return array
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('boolean', [$this, 'isBoolean']),
        ];
    }

    /**
     * @param array $context
     * @param $locale
     * @return bool
     */
    public function isActiveLocale(&$context, $locale)
    {
        if ((isset($context['submitLocale']) && $context['submitLocale'] == $locale)
            || (!isset($context['submitLocale']) && $context['currentLocale'] == $locale)
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isBoolean($value)
    {
        return is_bool($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simple_tests';
    }
}
