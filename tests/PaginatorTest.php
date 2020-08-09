<?php

declare(strict_types=1);

namespace Abb\Paginator\Tests;

use Abb\Paginator\Adapter\AdapterInterface;
use Abb\Paginator\Paginator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{

    /**
     * @var MockObject|AdapterInterface
     */
    private $adapter;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->adapter = $this->createMock(AdapterInterface::class);
        $this->paginator = new Paginator($this->adapter, 2);
    }

    /**
     * @param int $pageSize
     *
     * @testWith [0]
     *           [-1]
     *           [-20]
     */
    public function testNonPositivePageSizeIsRejected(int $pageSize): void
    {
        $this->expectException('Abb\Paginator\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Page size must be a positive integer.');

        new Paginator($this->adapter, $pageSize);
    }

    public function testPaginatorReturnsPaginationResult(): void
    {
        $this->adapter->method('getTotalItemCount')
            ->willReturn(8);

        $items = ['a', 'b'];
        $this->adapter->method('getItems')
            ->with(0, 2)
            ->willReturn($items);

        $result = $this->paginator->paginate(1);

        $this->assertEquals(1, $result->getCurrentPage());
        $this->assertEquals(8, $result->getTotalItemCount());
        $this->assertEquals(2, $result->getCurrentItemCount());
        $this->assertEquals(2, $result->getPageSize());
        $this->assertEquals($items, $result->getItems());
    }

    public function testPaginatorShouldCacheTheTotalItemCountFromTheAdapter(): void
    {
        $this->adapter->expects($this->once())
            ->method('getTotalItemCount')
            ->willReturn(10);

        $this->adapter->method('getItems')
            ->willReturn(['a', 'b']);

        $this->paginator->paginate(1);
        $this->paginator->paginate(2);
    }
}
