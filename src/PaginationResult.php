<?php

declare(strict_types=1);

namespace Abb\Paginator;

class PaginationResult
{

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var int
     */
    protected $totalItemCount;

    /**
     * @var array
     */
    protected $items;

    /**
     * Constructor
     *
     * @param int   $currentPage    Current page number
     * @param int   $pageSize       Page size
     * @param int   $totalItemCount Total number of items
     * @param array $items          Current page items
     */
    public function __construct(int $currentPage, int $pageSize, int $totalItemCount, array $items)
    {
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
        $this->totalItemCount = $totalItemCount;
        $this->items = $items;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getFirstPage(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return max(1, (int) ceil($this->totalItemCount / $this->pageSize));
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return bool
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return int
     */
    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    /**
     * @return int
     */
    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    /**
     * @return bool
     */
    public function hasToPaginate(): bool
    {
        return $this->totalItemCount > $this->pageSize;
    }

    /**
     * @return int
     */
    public function getTotalItemCount(): int
    {
        return $this->totalItemCount;
    }

    /**
     * @return int
     */
    public function getCurrentItemCount(): int
    {
        return count($this->getItems());
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
