<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service\Generic\Search;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GenericSearch
 * @package Nfq\AdminBundle\Service\Generic\Search
 */
abstract class GenericSearch implements GenericSearchInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var array */
    protected $searchFields = ['id',];

    /** @var string */
    protected $alias = 'search';

    /**
     * @param string[] $searchFields
     */
    public function __construct(array $searchFields = [])
    {
        $this->searchFields = $searchFields;
    }

    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return string[]
     */
    public function getSearchFields(): array
    {
        return $this->searchFields;
    }

    public function getResults(Request $request): Query
    {
        $queryBuilder = $this->createQueryBuilder($request);

        $query = $queryBuilder->getQuery();

        if (null !== ($count = $this->getResultCount($queryBuilder))) {
            $query
                ->setHint('knp_paginator.count', $count)
                ->setHint('knp_paginator.fetch_join_collection', false);
        }

        $this->setQueryHints($request, $query);

        return $query;
    }

    protected function getResultCount(QueryBuilder $queryBuilder): ?int
    {
        return null;
    }

    protected function createQueryBuilder(Request $request): QueryBuilder
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder($this->alias);

        $this->extendQuery($request, $queryBuilder);

        $this->getWhere($request, $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Used for custom query extending.
     */
    protected function extendQuery(Request $request, QueryBuilder $queryBuilder): void
    {
    }

    protected function setQueryHints(Request $request, Query $query): void
    {
    }

    /**
     * Builds main part of the search query. Use extendQuery() if you want to add additional conditions
     */
    private function getWhere(Request $request, QueryBuilder $queryBuilder): void
    {
        $token = $request->get('search', $request->get('q', null));

        if (empty($token)) {
            return;
        }

        $where = $queryBuilder->expr()->orX();
        $classMetaData = $this->getClassMetaData();

        $fields = $request->get('_sByFld', $this->getSearchFields());

        foreach ($fields as $idx => $_field) {
            $expr = null;

            [$field, $aliasedField] = $this->resolveField($_field);
            $fieldType = $classMetaData->getTypeOfField($field);

            switch ($fieldType) {
                case 'smallint':
                case 'bigint':
                case 'integer':
                case 'boolean':
                case 'decimal':
                case 'float':
                    if (is_numeric($token) || is_bool($token)) {
                        $expr = $queryBuilder->expr()->eq($aliasedField, $queryBuilder->expr()->literal($token));
                    }
                    break;
                case 'datetime':
                case 'timestamp':
                case 'date':
                case 'time':
                case 'year':
                    if ($this->hasValidDateSymbols($token)) {
                        $expr = $queryBuilder->expr()->like(
                            $aliasedField,
                            $queryBuilder->expr()->literal('%' . $token . '%')
                        );
                    }
                    break;
                default:
                    $expr = $queryBuilder->expr()->like(
                        $aliasedField,
                        $queryBuilder->expr()->literal('%' . $token . '%')
                    );
                    break;
            }

            $expr && $where->add($expr);
        }

        $queryBuilder->andWhere($where);
    }

    private function getClassMetaData(): ClassMetadata
    {
        $entityClass = $this->getRepository()->getClassName();
        return $this->em->getClassMetadata($entityClass);
    }

    private function hasValidDateSymbols(string $token): bool
    {
        return (bool)preg_match('~[0-9:\-\s]+~', $token);
    }

    private function resolveField(string $field): array
    {
        //Some alias is set, do nothing
        if (strpos($field, '.') !== false) {
            return [$field, $field];
        }

        return [$field, $this->alias . '.' . $field];
    }
}
