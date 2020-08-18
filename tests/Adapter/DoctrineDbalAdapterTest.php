<?php

declare(strict_types=1);

namespace Abb\Paginator\Tests\Adapter;

use Abb\Paginator\Adapter\DoctrineDbalAdapter;
use Doctrine\DBAL\Query\QueryBuilder;

class DoctrineDbalAdapterTest extends DoctrineDbalTestCase
{

    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->qb = new QueryBuilder($this->connection);
        $this->qb->select('p.*')->from('posts', 'p');
    }

    public function testNonSelectQueryIsRejected(): void
    {
        $this->expectException('Abb\Paginator\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Only SELECT queries can be paginated.');

        $this->qb->delete('posts');

        new DoctrineDbalAdapter($this->qb);
    }

    public function testAdapterReturnsTotalItemCount(): void
    {
        $adapter = new DoctrineDbalAdapter($this->qb);

        $this->assertSame(50, $adapter->getTotalItemCount());
    }

    public function testAdapterReturnsTotalItemCountWithCustomCountQueryBuilderModifier(): void
    {
        $adapter = new DoctrineDbalAdapter($this->qb, function (QueryBuilder $qb): QueryBuilder {
            return $qb->select('10 AS cnt')
                ->setMaxResults(1);
        });

        $this->assertSame(10, $adapter->getTotalItemCount());
    }

    public function testResultCountStaysConsistentAfterGettingItems(): void
    {
        $adapter = new DoctrineDbalAdapter($this->qb);

        $adapter->getItems(1, 10);

        $this->assertSame(50, $adapter->getTotalItemCount());
    }

    public function testGetItems(): void
    {
        $adapter = new DoctrineDbalAdapter($this->qb);

        $offset = 30;
        $limit = 10;

        $this->qb->setFirstResult($offset)->setMaxResults($limit);

        $this->assertSame($this->qb->execute()->fetchAll(), $adapter->getItems($offset, $limit));
    }

    public function testAdapterUsesClonedQuery(): void
    {
        $adapter = new DoctrineDbalAdapter($this->qb);

        // this query produces 250 rows
        $rows = $this->qb->join('p', 'comments', 'c', 'c.post_id = p.id')
            ->execute()
            ->fetchAll();

        $this->assertCount(250, $rows);

        // check if adapter uses cloned query which produces 50 rows
        $this->assertSame(50, $adapter->getTotalItemCount());
    }
}
