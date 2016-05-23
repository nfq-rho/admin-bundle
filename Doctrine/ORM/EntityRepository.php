<?php

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Doctrine\ORM;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;

/**
 * Class EntityRepository
 * @package Nfq\AdminBundle\Doctrine\ORM
 */
class EntityRepository extends BaseEntityRepository
{
    /**
     * @var bool
     */
    private $useQueryCache = true;

    /**
     * @param int|null $id
     * @return QueryBuilder
     */
    public function getQueryBuilder($id = null)
    {
        $alias = $this->getAlias();

        $qb = $this->createQueryBuilder($alias);

        if ($id) {
            $qb->where($alias . '.id = :id');
            $qb->setParameter('id', $id);
        }

        return $qb;
    }

    /**
     * @param array $criteria
     * @return Query
     */
    public function getQueryByCriteria(array $criteria)
    {
        $qb = $this->getQueryBuilder();

        $this->addArrayCriteria($qb, $criteria);

        return $qb->getQuery();
    }

    /**
     * @param array $criteria
     * @return object|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneByCriteria(array $criteria)
    {
        $query = $this->getQueryByCriteria($criteria);

        return $query->getOneOrNullResult();
    }

    /**
     * @param array $criteria
     * @param string $locale
     * @param bool $fallback
     * @return Query
     */
    public function getTranslatableQueryByCriteria(array $criteria, $locale, $fallback = true)
    {
        $qb = $this->getQueryBuilder();
        $this->addArrayCriteria($qb, $criteria);
        $query = $qb->getQuery();

        $this->setTranslatableHints($query, $locale, $fallback);

        return $query;
    }

    /**
     * @param bool $useCache
     * @return EntityRepository
     */
    public function setUseQueryCache($useCache)
    {
        $this->useQueryCache = $useCache;

        return $this;
    }

    /**
     * @param array $criteria
     * @param string $locale
     * @param bool $fallback
     * @return object|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneTranslatableByCriteria(array $criteria, $locale, $fallback = true)
    {
        $query = $this->getTranslatableQueryByCriteria($criteria, $locale, $fallback);

        $query->useQueryCache($this->useQueryCache);

        return $query->getOneOrNullResult();
    }

    /**
     * @param Query $query
     * @param string|null $locale
     * @param bool $fallback
     * @param bool $innerJoin
     */
    public function setTranslatableHints(Query $query, $locale, $fallback, $innerJoin = false)
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

    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     */
    public function addArrayCriteria(QueryBuilder $qb, array $criteria)
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

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'o';
    }
}
