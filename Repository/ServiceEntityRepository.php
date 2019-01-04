<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 * (c) NFQ Technologies UAB <info@nfq.com>
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

    /**
     * @param string[] $criteria
     */
    protected function addArrayCriteria(QueryBuilder $qb, array $criteria): void
    {
        $aliases = $qb->getAllAliases();

        $criteriaIndex = 0;
        foreach ($criteria as $field => $value) {
            $alias = $aliases[0];

            $negate = false;
            $fieldHasAlias = strpos($field, $alias . '.') !== false;

            if (strpos($field, '!') === 0) {
                $negate = true;
                $field = substr($field, 1);
            }

            $fieldKey = $fieldHasAlias ? $field : $alias . '.' . $field;
            $paramKey = ':param_' . $alias . '_' . md5($field) . '_' . $criteriaIndex;

            // Comparison operator detection
            if (\is_array($value) && \in_array($value[0], ['gt', 'gte', 'lt', 'lte'], true)) {
                $expr = $qb->expr()->{$value[0]}($fieldKey, $paramKey);
                $value = $value[1];
            } elseif (\is_object($value) || \is_array($value)) {
                $expr = $negate
                    ? $qb->expr()->notIn($fieldKey, $paramKey)
                    : $qb->expr()->in($fieldKey, $paramKey);
            } elseif (\is_string($value) && (strpos($value, '%') === 0 || substr($value, -1) === '%')) {
                $expr = $negate
                    ? $qb->expr()->notLike($fieldKey, $paramKey)
                    : $qb->expr()->like($fieldKey, $paramKey);
            } else {
                $expr = $negate
                    ? $qb->expr()->neq($fieldKey, $paramKey)
                    : $qb->expr()->eq($fieldKey, $paramKey);
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
