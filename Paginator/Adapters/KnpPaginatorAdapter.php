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
 * @property Paginator $paginator
 * @property PaginationInterface $pagination
 */
class KnpPaginatorAdapter extends AbstractPaginatorAdapter
{
    public static function supports(string $className): bool
    {
        return $className === Paginator::class;
    }

    protected function initPagination(): void
    {
        $this->pagination = $this->paginator->paginate(
            $this->target,
            $this->currentPage,
            $this->maxPerPage,
            $this->options
        );
    }

    protected function configurePagination(): void
    {
        //Remove `reopen_id` and `reopen_locale` from pagination parameters
        $this->pagination->setParam('reopen_id', null);
        $this->pagination->setParam('reopen_locale', null);
    }
}
