<?php

declare(strict_types=1);

namespace Abb\Paginator\Adapter;

use Abb\Paginator\Exception\InvalidArgumentException;
use Doctrine\DBAL\Connection;

class DoctrineDbalPlainSqlAdapter implements AdapterInterface
{

    const SELECT_QUERY_REGEXP = '~^(WITH.+\)\s*)?(SELECT.+)~misu';

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @var array
     */
    protected $sqlParams;

    /**
     * @var array
     */
    private $sqlParamsTypes;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * Constructor
     *
     * @param Connection $connection
     * @param string     $sql
     * @param array      $sqlParams
     * @param array      $sqlParamsTypes
     *
     * @throws InvalidArgumentException If a non-SELECT query is given
     */
    public function __construct(Connection $connection, string $sql, array $sqlParams = [], array $sqlParamsTypes = [])
    {
        $sql = $this->clearSql($sql);

        if (1 !== preg_match(self::SELECT_QUERY_REGEXP, $sql)) {
            throw new InvalidArgumentException('Only SELECT queries can be paginated.');
        }

        $this->connection = $connection;
        $this->sql = $sql;
        $this->sqlParams = $sqlParams;
        $this->sqlParamsTypes = $sqlParamsTypes;
    }

    /**
     * {@inheritDoc}
     */
    public function getTotalItemCount(): int
    {
        if (!isset($this->totalCount)) {
            $this->totalCount = (int) $this->connection->fetchColumn($this->buildCountSql(), $this->sqlParams, 0, $this->sqlParamsTypes);
        }

        return $this->totalCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems(int $offset, int $limit): array
    {
        $sql = $this->sql . ' LIMIT :_limit OFFSET :_offset';
        $sqlParams = array_merge($this->sqlParams, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]);

        return $this->connection->fetchAll($sql, $sqlParams, $this->sqlParamsTypes);
    }

    /**
     * Builds query to count results:
     * `SELECT count(*) FROM (sql) t_cnt`
     *
     * @return string
     */
    protected function buildCountSql(): string
    {
        return preg_replace(self::SELECT_QUERY_REGEXP, '$1SELECT count(*) FROM ($2) t_cnt', $this->sql);
    }

    /**
     * Clears SQL - removes leading and trailing spaces and semicolons
     *
     * @param string $sql
     *
     * @return string
     */
    protected function clearSql(string $sql): string
    {
        return trim($sql, "; \t\n\r\0\x0B");
    }
}
