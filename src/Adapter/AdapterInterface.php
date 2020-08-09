<?php

declare(strict_types=1);

namespace Abb\Paginator\Adapter;

interface AdapterInterface
{

    /**
     * Returns the number of results for the list.
     *
     * @return int
     */
    public function getTotalItemCount(): int;

    /**
     * Returns an slice of the results representing the current page of items in the list.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getItems(int $offset, int $limit): array;
}
