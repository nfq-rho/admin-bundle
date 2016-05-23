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
    /**
     * {@inheritdoc}
     */
    public static function supports($className)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setTarget($target)
    {
        if ($target instanceof Query) {
            $this->target = $target->getResult();

            return $this;
        }

        return parent::setTarget($target);
    }

    /**
     * @return int
     */
    public function getTotalItemCount()
    {
        return count($this->target);
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return $this->target;
    }
}
