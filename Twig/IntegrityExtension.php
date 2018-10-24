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
 * Class IntegrityExtension
 * @package Nfq\AdminBundle\Twig
 */
class IntegrityExtension extends \Twig_Extension
{
    /** @var array */
    private $bundles = [];

    public function __construct(array $bundles)
    {
        $this->bundles = $bundles;
    }

    public function getTests(): array
    {
        return [
            new \Twig_SimpleTest('enabled', [$this, 'isBundleLoaded']),
        ];
    }

    public function isBundleLoaded(string $name): bool
    {
        return array_key_exists($name, $this->bundles);
    }
}
