<?php

declare(strict_types=1);

namespace Abb\Paginator\Tests;

use Abb\Paginator\PaginationResult;
use PHPUnit\Framework\TestCase;

class PaginationResultTest extends TestCase
{

    public function testPaginationResultForSeveralPages(): void
    {
        $items = $this->generateItems(10);
        $result = new PaginationResult(1, 10, 55, $items);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(55, $result->getTotalItemCount());
        $this->assertEquals(10, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertEquals(6, $result->getPageCount());
        $this->assertEquals($items, $result->getItems());
    }

    public function testPaginationResultForOnlyOnePage(): void
    {
        $items = $this->generateItems(5);
        $result = new PaginationResult(1, 10, 5, $items);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(5, $result->getTotalItemCount());
        $this->assertEquals(5, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertEquals(1, $result->getPageCount());
        $this->assertEquals($items, $result->getItems());
    }

    public function testPaginationResultForEmptyData(): void
    {
        $result = new PaginationResult(1, 10, 0, []);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(0, $result->getTotalItemCount());
        $this->assertEquals(0, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertEquals(1, $result->getPageCount());
        $this->assertSame([], $result->getItems());
    }

    public function testPaginationResultWhenTotalItemCountEqualsPageSize(): void
    {
        $items = $this->generateItems(10);
        $result = new PaginationResult(1, 10, 10, $items);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(10, $result->getTotalItemCount());
        $this->assertEquals(10, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertEquals(1, $result->getPageCount());
        $this->assertSame($items, $result->getItems());
    }

    public function testPaginationResultWhenCurrentPageIsOutOfRange(): void
    {
        $result = new PaginationResult(5, 10, 15, []);

        $this->assertEquals(5, $result->getCurrentPage());
        $this->assertEquals(15, $result->getTotalItemCount());
        $this->assertEquals(0, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertEquals(2, $result->getPageCount());
        $this->assertSame([], $result->getItems());
    }

    private function generateItems(int $numRows): array
    {
        return array_pad([], $numRows, 'Item');
    }
}
