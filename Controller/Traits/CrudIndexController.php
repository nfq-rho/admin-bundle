<?php

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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CrudIndexController
 * @package Nfq\AdminBundle\Controller\Traits
 */
trait CrudIndexController
{
    /**
     * @var bool
     */
    protected $distinct = true;

    /**
     * Lists all entities.
     *
     * @Route("/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $options = [
            'distinct' => $this->distinct,
        ];

        $pagination = $this->get('nfq_admin.paginator.default')->getPagination(
                $request,
                $this->getIndexActionResults($request),
                $options
            );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * @param bool $distinct
     * @return $this
     */
    public function setDistinct($distinct)
    {
        $this->distinct = $distinct;
        return $this;
    }

    /**
     * Get's array of list
     * @deprecated - implement getIndexActionResults which return Query instead of result array
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    abstract protected function getIndexActionResultsArray(Request $request);

    /**
     * @param Request $request
     * @return Query
     */
    protected function getIndexActionResults(Request $request)
    {
        return $this->getIndexActionResultsArray($request);
    }
}
