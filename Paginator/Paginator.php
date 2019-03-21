<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Paginator;

use Symfony\Component\HttpFoundation\Request;
use Nfq\AdminBundle\Decorated\DecoratedTrait;
use Nfq\AdminBundle\Decorated\DecoratedInterface;
use Nfq\AdminBundle\Paginator\Adapters\DummyPaginatorAdapter;
use Nfq\AdminBundle\Paginator\Adapters\PaginatorAdapterInterface;

/**
 * Class Paginator
 * @package Nfq\AdminBundle\Paginator
 */
class Paginator implements DecoratedInterface
{
    use DecoratedTrait;

    /**
     * @var array
     */
    private $adapters = [];

    /**
     * @var int
     */
    private $maxPerPage;

    public function addAdapter(PaginatorAdapterInterface $adapter): void
    {
        $this->adapters[] = $adapter;
    }

    public function setMaxPerPage(int $maxPerPage): void
    {
        $this->maxPerPage = $maxPerPage;
    }

    public function getPagination(Request $request, $target, array $options = [])
    {
        $adapter = $this->getAdapter();

        $adapter
            ->setTarget($target)
            ->setCurrentPage($request->query->get('page', 1))
            ->setMaxPerPage($this->filterMaxPerPageValue($request))
            ->setOptions($options);

        return $adapter;
    }

    private function getAdapter(): PaginatorAdapterInterface
    {
        $decoratedPaginator = $this->getDecoratedService();
        $decoratedClass = get_class($decoratedPaginator);

        /** @var PaginatorAdapterInterface $adapter */
        foreach ($this->adapters as $adapter) {
            if ($adapter::supports($decoratedClass)) {
                $adapter->setPaginator($decoratedPaginator);

                return $adapter;
            }
        }

        return new DummyPaginatorAdapter();
    }

    private function filterMaxPerPageValue(Request $request): int
    {
        return (int)$request->query->get('perPage', $this->maxPerPage);
    }
}
