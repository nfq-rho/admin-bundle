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

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

trait TranslatableRepositoryTrait
{
    /** @var bool */
    private $useQueryCache = true;

    public function getTranslatableQueryByCriteria($criteria, ?string $locale, bool $fallback = true): Query
    {
        /** @var QueryBuilder $qb */
        $qb = $this->getQueryBuilder();

        $this->addCriteria($qb, $criteria);

        $query = $qb->getQuery();

        $this->setTranslatableHints($query, $locale, $fallback);

        return $query;
    }

    public function getTranslatableQueryByCriteriaSorted(
        $criteria,
        ?string $locale,
        bool $fallback = true,
        string $sortBy = 'id',
        string $sortOrder = 'ASC'
    ): Query {
        $qb = $this->getQueryBuilder();

        $this->addCriteria($qb, $criteria);

        $qb->orderBy($this->getAlias() . '.' . $sortBy, $sortOrder);

        $query = $qb->getQuery();

        $this->setTranslatableHints($query, $locale, $fallback);

        return $query;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneTranslatableByCriteria($criteria, ?string $locale, bool $fallback = true): ?object
    {
        $query = $this->getTranslatableQueryByCriteria($criteria, $locale, $fallback);

        $query->useQueryCache($this->useQueryCache);

        return $query->getOneOrNullResult();
    }

    public function setTranslatableHints(Query $query, ?string $locale, bool $fallback, bool $innerJoin = false): void
    {
        if (!class_exists(TranslatableListener::class)) {
            return;
        }

        if (!empty($locale)) {
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
        }

        if ($innerJoin) {
            $query->setHint(TranslatableListener::HINT_INNER_JOIN, $innerJoin);
        }

        $query
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class)
            ->setHint(TranslatableListener::HINT_FALLBACK, (int)$fallback);
    }

    public function setUseQueryCache(bool $useCache): self
    {
        $this->useQueryCache = $useCache;
        return $this;
    }
}
