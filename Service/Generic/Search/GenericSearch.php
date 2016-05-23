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
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GenericSearch
 * @package Nfq\AdminBundle\Service\Generic\Search
 */
abstract class GenericSearch implements GenericSearchInterface
{
    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var string
     */
    protected $alias = 'search';

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param array $fields
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param EntityManager $manager
     */
    public function setEntityManager(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults(Request $request, $defSort = 'search.id', $defDirection = 'DESC')
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

    /**
     * @param Query $query
     */
    protected function setQueryHints(Query $query)
    {

    }

    /**
     * Used for custom query extending.
     *
     * @param Request $request
     * @param QueryBuilder $queryBuilder
     */
    protected function extendQuery(Request $request, QueryBuilder $queryBuilder)
    {

    }

    /**
     * @param QueryBuilder $currentQueryBuilder
     * @return int|null
     */
    protected function getResultCount(QueryBuilder $currentQueryBuilder)
    {
        return null;
    }

    /**
     * @param Request $request
     * @param QueryBuilder $queryBuilder
     */
    protected function getWhere(Request $request, QueryBuilder $queryBuilder)
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

            list($field, $aliasedField) = $this->resolveField($_field);
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

    /**
     * @param Request $request
     * @return QueryBuilder
     */
    private function buildQuery(Request $request)
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder($this->alias);

        $this->getWhere($request, $queryBuilder);

        $this->extendQuery($request, $queryBuilder);

        return $queryBuilder;
    }

    /**
     * @param Request $request
     * @param string $defSort
     * @param string $defDirection
     */
    private function prepareRequest(Request $request, $defSort, $defDirection)
    {
        $this->locale = $request->getLocale();

        $sort = $request->query->get('sort', $defSort);
        $direction = strtoupper($request->query->get('direction', $defDirection));

        $request->query->add([
            'sort' => $sort,
            'direction' => $direction,
        ]);


        //This fix was added  due to the way KNPs paginator checks for sorting parameters
        $_GET['sort'] = $sort;
        $_GET['direction'] = $direction;
    }

    /**
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    private function getClassMetaData()
    {
        $entityClass = $this->getRepository()->getClassName();
        $classMetaData = $this->entityManager->getClassMetadata($entityClass);

        return $classMetaData;
    }

    /**
     * @param mixed $token
     * @return bool
     */
    private function hasValidDateSymbols($token)
    {
        return (bool)preg_match('~[0-9:\-\s]+~', $token);
    }

    /**
     * @param string $field
     * @return array
     */
    private function resolveField($field)
    {
        //Some alias is set, do nothing
        if (strpos($field, '.') !== false) {
            return [$field, $field];
        }

        return [$field, $this->alias . '.' . $field];
    }
}
