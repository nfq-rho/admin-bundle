<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service\Admin;

use Doctrine\ORM\Query;
use Nfq\AdminBundle\Service\Generic\Actions\GenericActionsInterface;
use Nfq\AdminBundle\Service\Generic\Search\GenericSearchInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface AdminManagerInterface
 * @package Nfq\AdminBundle\Service\Admin
 */
interface AdminManagerInterface
{
    public function setActions(GenericActionsInterface $actions): void;

    public function setSearch(GenericSearchInterface $search): void;

    public function getResults(Request $request): Query;
}
