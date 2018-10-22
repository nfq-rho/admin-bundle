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
    protected $fields = [];

    /** @var string */
    protected $alias = 'search';

    /** @var string */
    protected $locale;

    /**
     * @param string[] $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
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
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getResults(Request $request, string $defSort = 'search.id', string $defDirection = 'DESC'): Query
    {
        $this->prepareRequest($request, $defSort, $defDirection);

        $queryBuilder = $this->buildQuery($request);

        $query = $queryBuilder->getQuery();

        if (null !== ($count = $this->getResultCount($queryBuilder))) {
            $query
                ->setHint('knp_paginator.count', $count)
                ->setHint('knp_paginator.fetch_join_collection', false);
        }

        $this->setQueryHints($query);

        return $query;
    }

    protected function setQueryHints(Query $query): void
    {

    }

    /**
     * Used for custom query extending.
     */
    protected function extendQuery(Request $request, QueryBuilder $queryBuilder): void
    {

    }

    protected function getResultCount(QueryBuilder $currentQueryBuilder): ?int
    {
        return null;
    }

    protected function getWhere(Request $request, QueryBuilder $queryBuilder): void
    {
        $token = $request->get('search', null);
        ($token === null && $token !== '') && $token = $request->get('q', null);

        if ($token === null || $token === '') {
            return;
        }

        $where = $queryBuilder->expr()->orX();
        $classMetaData = $this->getClassMetaData();

        $fields = $request->get('_sByFld', $this->getFields());

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
                        $expr = $queryBuilder->expr()->like($aliasedField,
                            $queryBuilder->expr()->literal('%' . $token . '%'));
                    }
                    break;
                default:
                    $expr = $queryBuilder->expr()->like($aliasedField,
                        $queryBuilder->expr()->literal('%' . $token . '%'));
                    break;
            }

            $expr && $where->add($expr);
        }

        $queryBuilder->andWhere($where);
    }

    private function buildQuery(Request $request): QueryBuilder
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder($this->alias);

        $this->getWhere($request, $queryBuilder);

        $this->extendQuery($request, $queryBuilder);

        return $queryBuilder;
    }

    private function prepareRequest(Request $request, string $defSort, string $defDirection): void
    {
        $this->locale = $request->getLocale();

        $sort = $request->query->get(self::SORT_KEY, $defSort);
        $direction = strtoupper($request->query->get(self::DIRECTION_KEY, $defDirection));

        $request->query->add([
            self::SORT_KEY => $sort,
            self::DIRECTION_KEY => $direction,
        ]);

        //@TODO: review if this is still needed
        //This fix was added  due to the way KNPs paginator checks for sorting parameters
        $_GET[self::SORT_KEY] = $sort;
        $_GET[self::DIRECTION_KEY] = $direction;
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
