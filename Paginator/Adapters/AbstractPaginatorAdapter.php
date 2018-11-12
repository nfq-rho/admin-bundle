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
 * Class AbstractPaginatorAdapter
 * @package Nfq\AdminBundle\Paginator\Adapters
 */
abstract class AbstractPaginatorAdapter implements PaginatorAdapterInterface
{
    /**
     * Any data that will be paginated
     *
     * @var mixed
     */
    protected $target;

    /** @var int */
    protected $currentPage;

    /** @var int */
    protected $maxPerPage;

    /** @var string[] */
    protected $options;

    protected $paginator;

    protected $pagination;

    /**
     * This magic method proxies any missing method to pagination
     */
    public function __call($method, $args)
    {
        $methodAvailable = [$this->getPagination(), $method];
        if (\is_callable($methodAvailable)) {
            return \call_user_func_array($methodAvailable, $args);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPagination()
    {
        if (null === $this->pagination) {
            $this->initPagination();
            $this->configurePagination();
        }

        return $this->pagination;
    }

    /**
     * This method should contain pagination initialization code. And should return new pagination instance
     */
    abstract protected function initPagination(): void;

    /**
     * This method should have any code that is needed to customize pagination instance
     */
    protected function configurePagination(): void
    {
    }

    public function setCurrentPage(int $currentPage): PaginatorAdapterInterface
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function setMaxPerPage(int $maxPerPage): PaginatorAdapterInterface
    {
        $this->maxPerPage = $maxPerPage;

        return $this;
    }

    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }

    public function setPaginator($paginator): PaginatorAdapterInterface
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function setTarget($target): PaginatorAdapterInterface
    {
        $this->target = $target;

        return $this;
    }

    public function setOptions(array $options): PaginatorAdapterInterface
    {
        $this->options = $options;

        return $this;
    }

    public function getShowingFrom(): int
    {
        return $this->getTotalItemCount() > 0
            ? ($this->currentPage - 1) * $this->maxPerPage + 1
            : 0;
    }

    public function getShowingTo(): int
    {
        $showingTo = $this->currentPage * $this->maxPerPage;

        if ($showingTo > $this->getTotalItemCount()) {
            $showingTo = $this->getTotalItemCount();
        }

        return $showingTo;
    }
}
