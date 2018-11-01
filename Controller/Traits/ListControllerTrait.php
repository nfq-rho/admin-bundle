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
                $this->getIndexActionResults($request),
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
}
