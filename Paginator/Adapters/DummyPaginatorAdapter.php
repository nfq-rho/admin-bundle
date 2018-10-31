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

use Doctrine\ORM\Query;

/**
 * Class DummyPaginatorAdapter
 * @package Nfq\AdminBundle\Paginator\Adapters
 */
class DummyPaginatorAdapter extends AbstractPaginatorAdapter
{
    public static function supports(string $className): bool
    {
        return true;
    }

    public function initPagination(): void
    {
    }

    public function setTarget($target): PaginatorAdapterInterface
    {
        if ($target instanceof Query) {
            $this->target = $target->getResult();

            return $this;
        }

        return parent::setTarget($target);
    }

    public function getTotalItemCount(): int
    {
        return count($this->target);
    }
}
