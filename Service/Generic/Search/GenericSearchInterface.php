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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface GenericSearchInterface
 * @package Nfq\AdminBundle\Service\Generic\Search
 */
interface GenericSearchInterface
{
    public const SORT_KEY = 'sort';
    public const DIRECTION_KEY = 'direction';

    public function setEntityManager(EntityManagerInterface $em): void;

    public function getEntityManager(): EntityManagerInterface;

    /**
     * @return string[]
     */
    public function getSearchFields(): array;

    public function getResults(Request $request): Query;

    public function getRepository(): EntityRepository;
}
