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
     */
    public static function supports(string $class): bool;

    public function setCurrentPage(int $currentPage): PaginatorAdapterInterface;

    public function setMaxPerPage(int $maxPerPage): PaginatorAdapterInterface;

    public function getMaxPerPage(): int;

    public function getShowingFrom(): int;

    public function getShowingTo(): int;

    /**
     * @param mixed $target
     */
    public function setTarget($target): PaginatorAdapterInterface;

    /**
     * @param string[] $options
     */
    public function setOptions(array $options): PaginatorAdapterInterface;

    public function setPaginator(object $paginator): PaginatorAdapterInterface;
}
