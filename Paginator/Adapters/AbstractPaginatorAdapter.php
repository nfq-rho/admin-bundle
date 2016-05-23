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

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var int
     */
    protected $maxPerPage;

    /**
     * @var array
     */
    protected $options;

    protected $paginator;

    protected $pagination;

    /**
     * This magic method proxies any missing method to pagination
     */
    public function __call($method, $args)
    {
        $methodAvailable = [$this->getPagination(), $method];
        if (is_callable($methodAvailable)) {
            return call_user_func_array($methodAvailable, $args);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPagination()
    {
        if (is_null($this->pagination)) {
            $this->pagination = $this->initPagination();
            $this->configurePagination();
        }

        return $this->pagination;
    }

    /**
     * This method should contain pagination initialization code. And should return new pagination instance
     *
     * @return
     */
    abstract protected function initPagination();

    /**
     * This method should have any code that is needed to customize pagination instance
     *
     * @return void
     */
    protected function configurePagination()
    {

    }

    /**
     * @inheritdoc
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * @inheritdoc
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShowingFrom()
    {
        return $this->getTotalItemCount() > 0
            ? ($this->currentPage - 1) * $this->maxPerPage + 1
            : 0;
    }

    /**
     * @inheritdoc
     */
    public function getShowingTo()
    {
        $showingTo = $this->currentPage * $this->maxPerPage;

        if($showingTo > $this->getTotalItemCount()) {
            $showingTo = $this->getTotalItemCount();
        }

        return $showingTo;
    }
}
