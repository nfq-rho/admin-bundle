<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service\Generic\Search;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface GenericSearchInterface
 * @package Nfq\AdminBundle\Service\Generic\Search
 */
interface GenericSearchInterface
{
    /**
     * @param EntityManager $manager
     */
    public function setEntityManager(EntityManager $manager);

    /**
     * @return array
     */
    public function getFields();

    /**
     * @param Request $request
     * @return Query
     */
    public function getResults(Request $request);

    /**
     * @return EntityRepository
     */
    public function getRepository();
}
