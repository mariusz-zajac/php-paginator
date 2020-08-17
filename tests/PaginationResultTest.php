<?php

declare(strict_types=1);

namespace Abb\Paginator\Tests;

use Abb\Paginator\PaginationResult;
use PHPUnit\Framework\TestCase;

class PaginationResultTest extends TestCase
{

    public function testPaginationResultForFirstPage(): void
    {
        $items = $this->generateItems(10);
        $result = new PaginationResult(1, 10, 50, $items);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(50, $result->getTotalItemCount());
        $this->assertEquals(10, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertFalse($result->hasPreviousPage());
        $this->assertNull($result->getPreviousPage());
        $this->assertTrue($result->hasNextPage());
        $this->assertEquals(2, $result->getNextPage());
        $this->assertEquals(1, $result->getFirstPage());
        $this->assertEquals(5, $result->getLastPage());
        $this->assertEquals($items, $result->getItems());
        $this->assertTrue($result->hasToPaginate());
    }

    public function testPaginationResultForLastPage(): void
    {
        $items = $this->generateItems(1);
        $result = new PaginationResult(6, 10, 51, $items);

        $this->assertEquals(6, $result->getCurrentPage());
        $this->assertEquals(51, $result->getTotalItemCount());
        $this->assertEquals(1, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertTrue($result->hasPreviousPage());
        $this->assertEquals(5, $result->getPreviousPage());
        $this->assertFalse($result->hasNextPage());
        $this->assertNull($result->getNextPage());
        $this->assertEquals(1, $result->getFirstPage());
        $this->assertEquals(6, $result->getLastPage());
        $this->assertEquals($items, $result->getItems());
        $this->assertTrue($result->hasToPaginate());
    }

    public function testPaginationResultForMiddlePage(): void
    {
        $items = $this->generateItems(10);
        $result = new PaginationResult(3, 10, 55, $items);

        $this->assertEquals(3, $result->getCurrentPage());
        $this->assertEquals(55, $result->getTotalItemCount());
        $this->assertEquals(10, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertTrue($result->hasPreviousPage());
        $this->assertEquals(2, $result->getPreviousPage());
        $this->assertTrue($result->hasNextPage());
        $this->assertEquals(4, $result->getNextPage());
        $this->assertEquals(1, $result->getFirstPage());
        $this->assertEquals(6, $result->getLastPage());
        $this->assertEquals($items, $result->getItems());
        $this->assertTrue($result->hasToPaginate());
    }

    public function testPaginationResultForOnlyOnePage(): void
    {
        $items = $this->generateItems(5);
        $result = new PaginationResult(1, 10, 5, $items);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(5, $result->getTotalItemCount());
        $this->assertEquals(5, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertFalse($result->hasPreviousPage());
        $this->assertNull($result->getPreviousPage());
        $this->assertFalse($result->hasNextPage());
        $this->assertNull($result->getNextPage());
        $this->assertEquals(1, $result->getFirstPage());
        $this->assertEquals(1, $result->getLastPage());
        $this->assertEquals($items, $result->getItems());
        $this->assertFalse($result->hasToPaginate());
    }

    public function testPaginationResultForEmptyData(): void
    {
        $result = new PaginationResult(1, 10, 0, []);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(0, $result->getTotalItemCount());
        $this->assertEquals(0, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertFalse($result->hasPreviousPage());
        $this->assertNull($result->getPreviousPage());
        $this->assertFalse($result->hasNextPage());
        $this->assertNull($result->getNextPage());
        $this->assertEquals(1, $result->getFirstPage());
        $this->assertEquals(1, $result->getLastPage());
        $this->assertSame([], $result->getItems());
        $this->assertFalse($result->hasToPaginate());
    }

    public function testPaginationResultWhenTotalItemCountEqualsPageSize(): void
    {
        $items = $this->generateItems(10);
        $result = new PaginationResult(1, 10, 10, $items);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(10, $result->getTotalItemCount());
        $this->assertEquals(10, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertFalse($result->hasPreviousPage());
        $this->assertNull($result->getPreviousPage());
        $this->assertFalse($result->hasNextPage());
        $this->assertNull($result->getNextPage());
        $this->assertEquals(1, $result->getFirstPage());
        $this->assertEquals(1, $result->getLastPage());
        $this->assertSame($items, $result->getItems());
        $this->assertFalse($result->hasToPaginate());
    }

    public function testPaginationResultWhenCurrentPageIsOutOfRange(): void
    {
        $result = new PaginationResult(5, 10, 15, []);

        $this->assertEquals(5, $result->getCurrentPage());
        $this->assertEquals(15, $result->getTotalItemCount());
        $this->assertEquals(0, $result->getItemCount());
        $this->assertEquals(10, $result->getPageSize());
        $this->assertTrue($result->hasPreviousPage());
        $this->assertEquals(4, $result->getPreviousPage());
        $this->assertFalse($result->hasNextPage());
        $this->assertNull($result->getNextPage());
        $this->assertEquals(1, $result->getFirstPage());
        $this->assertEquals(2, $result->getLastPage());
        $this->assertSame([], $result->getItems());
        $this->assertTrue($result->hasToPaginate());
    }

    private function generateItems(int $numRows): array
    {
        return array_pad([], $numRows, 'Item');
    }
}
