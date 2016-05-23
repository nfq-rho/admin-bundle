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
 * Class IsLoadedExtension
 * @package Nfq\AdminBundle\Twig
 */
class IsLoadedExtension extends \Twig_Extension implements \Twig_Extension_InitRuntimeInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('loaded', [$this, 'hasExtension']),
        ];
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    function hasExtension($name)
    {
        return $this->environment->hasExtension($name);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'nfq_admin_extension_exists';
    }
}
