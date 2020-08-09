<?php

declare(strict_types=1);

namespace Abb\Paginator;

use Abb\Paginator\Adapter\AdapterInterface;
use Abb\Paginator\Exception\InvalidArgumentException;

class Paginator
{

    const PAGE_SIZE = 10;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * Constructor
     *
     * @param AdapterInterface $adapter  Pagination adapter
     * @param int              $pageSize Page size (default 10)
     *
     * @throws InvalidArgumentException If zero or a negative page size is given
     */
    public function __construct(AdapterInterface $adapter, int $pageSize = self::PAGE_SIZE)
    {
        if ($pageSize < 1) {
            throw new InvalidArgumentException('Page size must be a positive integer.');
        }

        $this->adapter = $adapter;
        $this->pageSize = $pageSize;
    }

    /**
     * Paginate results
     *
     * @param int $page Current page number
     *
     * @return PaginationResult
     */
    public function paginate(int $page = 1): PaginationResult
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $this->pageSize;

        $totalCount = $this->getTotalCount();
        $items = $this->adapter->getItems($offset, $this->pageSize);

        return new PaginationResult($page, $this->pageSize, $totalCount, $items);
    }

    /**
     * Returns total number of results
     *
     * @return int
     */
    protected function getTotalCount(): int
    {
        if (!isset($this->totalCount)) {
            $this->totalCount = $this->adapter->getTotalItemCount();
        }

        return $this->totalCount;
    }
}
