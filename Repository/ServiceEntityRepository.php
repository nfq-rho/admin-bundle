<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository as BaseServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ServiceEntityRepository
 * @package Nfq\AdminBundle\Repository
 */
class ServiceEntityRepository extends BaseServiceEntityRepository
{
    public function createEntity()
    {
        $class = $this->getClassName();
        return new $class();
    }

    /**
     * @param mixed $id
     */
    public function getQueryBuilder($id = null): QueryBuilder
    {
        $alias = $this->getAlias();

        $qb = $this->createQueryBuilder($alias);

        if ($id) {
            $qb->where($alias . '.id = :id');
            $qb->setParameter('id', $id);
        }

        return $qb;
    }

    public function addCriteria(QueryBuilder $qb, $criteria): void
    {
        if (\is_array($criteria)) {
            $this->addArrayCriteria($qb, $criteria);
        } elseif ($criteria instanceof Criteria) {
            $qb->addCriteria($criteria);
        } else {
            throw new \InvalidArgumentException('Criteria has to be array or Query\Expr');
        }
    }

    protected function addArrayCriteria(QueryBuilder $qb, array $criteria): void
    {
        $aliases = $qb->getAllAliases();

        $criteriaIndex = 0;
        foreach ($criteria as $key => $value) {
            $alias = $aliases[0];

            $negate = false;

            if (strpos($key, '!') === 0) {
                $negate = true;
                $key = substr($key, 1);
            }

            if (strpos($key, '.') !== false) {
                [$alias, $key] = explode('.', $key);

                if (!\in_array($alias, $aliases, true)) {
                    throw new \InvalidArgumentException("Invalid alias detected `{$alias}");
                }
            }

            $paramKey = ':param_' . $alias . '_' . $key . '_' . $criteriaIndex;
            if (\is_object($value) || \is_array($value)) {
                $expr = $negate
                    ? $qb->expr()->notIn($alias . '.' . $key, $paramKey)
                    : $qb->expr()->in($alias . '.' . $key, $paramKey);
            } elseif (\is_string($value) && (strpos($value, '%') === 0 || substr($value, -1) === '%')) {
                $expr = $negate
                    ? $qb->expr()->notLike($alias . '.' . $key, $paramKey)
                    : $qb->expr()->like($alias . '.' . $key, $paramKey);
            } else {
                $expr = $negate
                    ? $qb->expr()->neq($alias . '.' . $key, $paramKey)
                    : $qb->expr()->eq($alias . '.' . $key, $paramKey);
            }

            $qb->andWhere($expr);

            $qb->setParameter($paramKey, $value);

            $criteriaIndex++;
        }
    }

    public function getAlias(): string
    {
        return 'o';
    }
}
