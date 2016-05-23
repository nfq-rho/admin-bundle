<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Paginator\Adapters;

/**
 * Interface PaginatorAdapterInterface
 * @package Nfq\AdminBundle\Paginator\Adapters
 */
interface PaginatorAdapterInterface
{
    /**
     * Checks if given class is supported by this adapter.
     *
     * @param string $class
     *
     * @return bool
     */
    public static function supports($class);

    /**
     * @param int $currentPage
     *
     * @return PaginatorAdapterInterface
     */
    public function setCurrentPage($currentPage);

    /**
     * @param int $maxPerPage
     *
     * @return PaginatorAdapterInterface
     */
    public function setMaxPerPage($maxPerPage);

    /**
     * @return int
     */
    public function getMaxPerPage();

    /**
     * @return int
     */
    public function getShowingFrom();

    /**
     * @return int
     */
    public function getShowingTo();

    /**
     * @param mixed $target
     *
     * @return PaginatorAdapterInterface
     */
    public function setTarget($target);

    /**
     * @param array $options
     *
     * @return PaginatorAdapterInterface
     */
    public function setOptions(array $options);

    /**
     * @param object $paginator
     *
     * @return PaginatorAdapterInterface
     */
    public function setPaginator($paginator);
}
