<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Controller\Traits;

use Doctrine\ORM\Query;
use Nfq\AdminBundle\Paginator\Paginator;
use Nfq\AdminBundle\Service\Generic\Search\GenericSearchInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait ListControllerTrait
 * @package Nfq\AdminBundle\Controller\Traits
 */
trait ListControllerTrait
{
    /** @var Paginator */
    private $paginator;

    /** @var bool */
    protected $distinct = true;

    /** @var string */
    protected $defaultSortColumn = 'search.id';

    /** @var string */
    protected $defaultSortDirection = 'DESC';

    /**
     * @required
     */
    public function setPaginator(Paginator $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * @Template()
     * @return array|Response
     */
    public function indexAction(Request $request)
    {
        $options = [
            'distinct' => $this->distinct,
        ];

        $pagination = $this->paginator->getPagination(
            $request,
            $this->getIndexActionResults(
                $this->prepareRequest($request)
            ),
            $options
        );

        return [
            'pagination' => $pagination,
        ];
    }

    protected function setDistinct(bool $distinct): void
    {
        $this->distinct = $distinct;
    }

    /**
     * @return Query|array|iterable
     */
    abstract protected function getIndexActionResults(Request $request);

    private function prepareRequest(Request $request): Request
    {
        $sort = $request->query->get(GenericSearchInterface::SORT_KEY, $this->defaultSortColumn);
        $direction = strtoupper($request->query->get(
            GenericSearchInterface::DIRECTION_KEY,
            $this->defaultSortDirection
        ));

        $request->query->add([
            GenericSearchInterface::SORT_KEY => $sort,
            GenericSearchInterface::DIRECTION_KEY => $direction,
        ]);

        //@TODO: review if this is still needed
        //This fix was added  due to the way KnpPaginator checks for sorting parameters
        $_GET[GenericSearchInterface::SORT_KEY] = $sort;
        $_GET[GenericSearchInterface::DIRECTION_KEY] = $direction;

        return $request;
    }
}
