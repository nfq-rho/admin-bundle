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
    use DecoratedTrait {
        setDecoratedService as parentSetDecoratedService;
    }

    private $tempDecorated;

    /**
     * @var
     */
    private $adapters;

    /**
     * @var int
     */
    private $maxPerPage;

    /**
     * {@inheritdoc}
     */
    public function setDecoratedService($decorated)
    {
        $this->tempDecorated = $decorated;
    }

    /**
     * @param PaginatorAdapterInterface $adapter
     */
    public function addAdapter(PaginatorAdapterInterface $adapter)
    {
        $this->adapters[] = $adapter;
    }

    /**
     * @return PaginatorAdapterInterface
     */
    private function getAdapter()
    {
        $decoratedClass = get_class($this->tempDecorated);

        /** @var PaginatorAdapterInterface $adapter */
        foreach ($this->adapters as $adapter) {
            if ($adapter::supports($decoratedClass)) {

                $adapter->setPaginator($this->tempDecorated);

                return $adapter;
            }
        }

        return new DummyPaginatorAdapter();
    }

    /**
     * @param int $maxPerPage
     *
     * @return Paginator
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;

        return $this;
    }

    /**
     * @param Request $request
     * @param string $target
     * @param array $options
     * @return mixed
     */
    public function getPagination(Request $request, $target, array $options = [])
    {
        $adapter = $this->getAdapter();

        $adapter
            ->setTarget($target)
            ->setCurrentPage($request->query->get('page', 1))
            ->setMaxPerPage($this->filterMaxPerPageValue($request))
            ->setOptions($options);

        $this->parentSetDecoratedService($adapter);

        return $this->getDecoratedService();
    }

    /**
     * @param Request $request
     * @return int
     */
    private function filterMaxPerPageValue(Request $request)
    {
        $value = $request->query->get('perPage', null);
        return is_numeric($value) && $value > 0  ? (int) $value : $this->maxPerPage;
    }
}
