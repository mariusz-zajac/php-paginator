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
     * @param int   $pageSize       Maximum number of items per page
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
     * Returns current page number
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Returns number of pages
     *
     * @return int
     */
    public function getPageCount(): int
    {
        return max(1, (int) ceil($this->totalItemCount / $this->pageSize));
    }

    /**
     * Returns maximum number of items per page
     *
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * Returns total number of items
     *
     * @return int
     */
    public function getTotalItemCount(): int
    {
        return $this->totalItemCount;
    }

    /**
     * Returns number of items for the current page
     *
     * @return int
     */
    public function getItemCount(): int
    {
        return count($this->getItems());
    }

    /**
     * Returns items for the current page
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
