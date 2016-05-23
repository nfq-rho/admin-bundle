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

use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class KnpPaginatorAdapter
 * @package Nfq\AdminBundle\Paginator\Adapters
 */
class KnpPaginatorAdapter extends AbstractPaginatorAdapter
{
    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var PaginationInterface
     */
    protected $pagination;

    /**
     * {@inheritdoc}
     */
    public static function supports($className)
    {
        return $className === 'Knp\\Component\\Pager\\Paginator';
    }

    /**
     * @return PaginationInterface
     */
    protected function initPagination()
    {
        return $this->paginator->paginate(
            $this->target,
            $this->currentPage,
            $this->maxPerPage,
            $this->options
        );
    }

    protected function configurePagination()
    {
        //Remove `reopen_id` from pagination parameters
        $this->pagination->setParam('reopen_id', null);
    }
}
