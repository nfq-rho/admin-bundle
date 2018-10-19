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
 * Class TestExtension
 * @package Nfq\AdminBundle\Twig
 */
class TestExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('isActiveLocale', [$this, 'isActiveLocale']),
        ];
    }

    public function getTests(): array
    {
        return [
            new \Twig_SimpleTest('boolean', [$this, 'isBoolean']),
        ];
    }

    public function isActiveLocale(&$context, string $locale): bool
    {
        return (isset($context['submitLocale']) && $context['submitLocale'] == $locale)
            || (!isset($context['submitLocale']) && $context['currentLocale'] == $locale);
    }

    public function isBoolean($value): bool
    {
        return is_bool($value);
    }

    public function getName(): string
    {
        return 'simple_tests';
    }
}
