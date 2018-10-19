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
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ServiceEntityRepository
 * @package Nfq\AdminBundle\Repository
 */
class ServiceEntityRepository extends BaseServiceEntityRepository
{
    /** @var bool */
    private $useQueryCache = true;

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

    public function getQueryByCriteria(array $criteria): Query
    {
        $qb = $this->getQueryBuilder();

        $this->addArrayCriteria($qb, $criteria);

        return $qb->getQuery();
    }

    /**
     * @return object|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneByCriteria(array $criteria)
    {
        $query = $this->getQueryByCriteria($criteria);

        return $query->getOneOrNullResult();
    }

    public function getTranslatableQueryByCriteria(array $criteria, ?string $locale, bool $fallback = true): Query
    {
        $qb = $this->getQueryBuilder();
        $this->addArrayCriteria($qb, $criteria);

        $query = $qb->getQuery();

        $this->setTranslatableHints($query, $locale, $fallback);

        return $query;
    }

    public function getTranslatableQueryByCriteriaSorted(
        array $criteria,
        string $locale,
        bool $fallback = true,
        string $sortBy = 'id',
        string $sortOrder = 'ASC'
    ): Query {
        $qb = $this->getQueryBuilder();
        $this->addArrayCriteria($qb, $criteria);
        $qb->orderBy($alias . '.' . $sort, $order);

        $query = $qb->getQuery();

        $this->setTranslatableHints($query, $locale, $fallback);

        return $query;
    }

    public function setUseQueryCache(bool $useCache): self
    {
        $this->useQueryCache = $useCache;
        return $this;
    }

    /**
     * @return object|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneTranslatableByCriteria(array $criteria, ?string $locale, bool $fallback = true)
    {
        $query = $this->getTranslatableQueryByCriteria($criteria, $locale, $fallback);

        $query->useQueryCache($this->useQueryCache);

        return $query->getOneOrNullResult();
    }

    public function setTranslatableHints(Query $query, ?string $locale, bool $fallback, bool $innerJoin = false): void
    {
        if (!class_exists('Gedmo\\Translatable\\TranslatableListener')) {
            return;
        }

        if (!empty($locale)) {
            $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
        }

        if ($innerJoin) {
            $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_INNER_JOIN, $innerJoin);
        }

        $query
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, (int)$fallback);
    }

    public function addArrayCriteria(QueryBuilder $qb, array $criteria): void
    {
        $aliases = $qb->getAllAliases();

        foreach ($criteria as $key => $value) {
            $alias = $aliases[0];
            if (strpos($key, '.') !== false) {
                list($alias, $key) = explode('.', $key);

                if (!in_array($alias, $aliases)) {
                    throw new \InvalidArgumentException("Invalid alias detected `{$alias}");
                }
            }

            $paramKey = ':param_' . $alias . $key;
            if (is_object($value) || is_array($value)) {
                $qb->andWhere($qb->expr()->in($alias . '.' . $key, $paramKey));
            } elseif (strpos($value, '%') === 0 || substr($value, -1) === '%') {
                $qb->andWhere($qb->expr()->like($alias . '.' . $key, $paramKey));
            } else {
                $qb->andWhere($qb->expr()->eq($alias . '.' . $key, $paramKey));
            }

            $qb->setParameter($paramKey, $value);
        }
    }

    public function getAlias(): string
    {
        return 'o';
    }
}
