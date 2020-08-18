<?php

declare(strict_types=1);

namespace Abb\Paginator\Adapter;

use Abb\Paginator\Exception\UnsupportedQueryBuilderTypeException;
use Doctrine\DBAL\Query\QueryBuilder;

class DoctrineDbalAdapter implements AdapterInterface
{

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var QueryBuilder
     */
    protected $countQueryBuilder;

    /**
     * @var callable
     */
    protected $countQueryBuilderModifier;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * Constructor
     *
     * A callable modifier to modify the query builder to count the results should have a signature of
     * `function (QueryBuilder $queryBuilder): QueryBuilder {}`
     *
     * @param QueryBuilder  $queryBuilder
     * @param callable|null $countQueryBuilderModifier
     *
     * @throws UnsupportedQueryBuilderTypeException If a non-SELECT query is given
     */
    public function __construct(QueryBuilder $queryBuilder, callable $countQueryBuilderModifier = null)
    {
        if (QueryBuilder::SELECT !== $queryBuilder->getType()) {
            throw new UnsupportedQueryBuilderTypeException('Only SELECT queries can be paginated.');
        }

        $this->queryBuilder = clone $queryBuilder;
        $this->countQueryBuilder = clone $queryBuilder;
        $this->countQueryBuilderModifier = $countQueryBuilderModifier ?: [$this, 'defaultCountQueryBuilderModifier'];
    }

    /**
     * {@inheritDoc}
     */
    public function getTotalItemCount(): int
    {
        if (!isset($this->totalCount)) {
            $this->totalCount = (int) $this->prepareCountQueryBuilder()->execute()->fetchColumn();
        }

        return $this->totalCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems(int $offset, int $limit): array
    {
        return $this->queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->execute()
            ->fetchAll();
    }

    /**
     * Default count query builder modifier.
     *
     * Prepares query to count results:
     * `SELECT count(*) FROM (subquery) t LIMIT 1`
     *
     * @param QueryBuilder $queryBuilder Query to fetch results
     *
     * @return QueryBuilder
     */
    protected function defaultCountQueryBuilderModifier(QueryBuilder $queryBuilder): QueryBuilder
    {
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        $paramTypes = $queryBuilder->getParameterTypes();

        $countQb = $queryBuilder->getConnection()->createQueryBuilder();

        return $countQb->select('count(*)')
            ->from('(' . $sql . ')', 't')
            ->setParameters($params, $paramTypes)
            ->setMaxResults(1);
    }

    /**
     * Prepare query builder to count results using query builder modifier
     *
     * @return QueryBuilder
     */
    protected function prepareCountQueryBuilder(): QueryBuilder
    {
        $qb = $this->countQueryBuilder;
        $modifier = $this->countQueryBuilderModifier;

        return $modifier($qb);
    }
}
