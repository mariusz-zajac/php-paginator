<?php

declare(strict_types=1);

namespace Abb\Paginator\Tests\Adapter;

use Abb\Paginator\Adapter\DoctrineDbalPlainSqlAdapter;

class DoctrineDbalPlainSqlAdapterTest extends DoctrineDbalTestCase
{

    /**
     * @testWith
     * ["UPDATE posts SET content = 'test'"]
     * ["DELETE FROM posts WHERE published = false"]
     * ["WITH t AS (SELECT id FROM posts WHERE username = 'John Doe') DELETE FROM posts WHERE id IN (SELECT id FROM t)"]
     */
    public function testNonSelectQueryIsRejected(string $sql): void
    {
        $this->expectException('Abb\Paginator\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Only SELECT queries can be paginated.');

        new DoctrineDbalPlainSqlAdapter($this->connection, $sql);
    }

    public function testAdapterReturnsTotalItemCount(): void
    {
        $sql = 'SELECT p.* FROM posts p';
        $adapter = new DoctrineDbalPlainSqlAdapter($this->connection, $sql);

        $this->assertSame(50, $adapter->getTotalItemCount());
    }

    public function testResultCountStaysConsistentAfterGettingItems(): void
    {
        $sql = 'SELECT p.* FROM posts p';
        $adapter = new DoctrineDbalPlainSqlAdapter($this->connection, $sql);

        $adapter->getItems(10, 10);

        $this->assertSame(50, $adapter->getTotalItemCount());
    }

    public function testGetItems(): void
    {
        $sql = 'SELECT p.* FROM posts p';
        $adapter = new DoctrineDbalPlainSqlAdapter($this->connection, $sql);

        $expectedRows = $this->connection->fetchAll('SELECT p.* FROM posts p LIMIT 10 OFFSET 30');

        $this->assertSame($expectedRows, $adapter->getItems(30, 10));
    }

    public function testGetItemsByParams(): void
    {
        $sql = '
            SELECT p.*
            FROM posts p
            JOIN comments c ON c.post_id = p.id
            WHERE c.username = :username
            ORDER BY p.id, c.id;
        ';
        $sqlParams = ['username' => 'Jane Doe'];
        $adapter = new DoctrineDbalPlainSqlAdapter($this->connection, $sql, $sqlParams);

        $expectedRows = $this->connection->fetchAll("
            SELECT p.*
            FROM posts p
            JOIN comments c ON c.post_id = p.id
            WHERE c.username = 'Jane Doe'
            ORDER BY p.id, c.id
            LIMIT 5 
            OFFSET 10
        ");

        $this->assertSame($expectedRows, $adapter->getItems(10, 5));
    }

    public function testBuildCountSql(): void
    {
        $sql = '
            WITH posts_with_comments AS (
                SELECT p.id, p.content, c.username, c.content AS comment
                FROM posts p
                LEFT JOIN comments c ON c.post_id = p.id
            )
            SELECT * FROM posts_with_comments;
        ';
        $expectedCountSql = 'WITH posts_with_comments AS (
                SELECT p.id, p.content, c.username, c.content AS comment
                FROM posts p
                LEFT JOIN comments c ON c.post_id = p.id
            )
            SELECT count(*) FROM (SELECT * FROM posts_with_comments) t_cnt';

        $adapter = new class($this->connection, $sql) extends DoctrineDbalPlainSqlAdapter {
            public function buildCountSql(): string
            {
                return parent::buildCountSql();
            }
        };

        $this->assertEquals($expectedCountSql, $adapter->buildCountSql($sql));
    }
}
