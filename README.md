# PHP Paginator

Simple and flexible PHP paginator inspired by [Pagerfanta](https://github.com/BabDev/Pagerfanta)
and [Symfony Demo Paginator](https://github.com/symfony/demo/blob/master/src/Pagination/Paginator.php).

## Requirements

* PHP 7.1 or higher.

## Installation

The recommended way to install is through [Composer](http://getcomposer.org).

```bash
$ composer require abb/paginator
```

## Available adapters

### Doctrine DBAL Adapter

Example of usage:

```php
<?php

use Abb\Paginator\Adapter\DoctrineDbalAdapter;
use Abb\Paginator\Paginator;
use Doctrine\DBAL\DriverManager;

$params = [/*...*/];
$connection = DriverManager::getConnection($params);

$qb = $connection->createQueryBuilder()
    ->select('p.*')
    ->from('posts', 'p')
    ->where('p.published = true');

$adapter = new DoctrineDbalAdapter($qb);
$pageSize = 5; // default 10
$paginator = new Paginator($adapter, $pageSize);
$page = 1;
$paginationResult = $paginator->paginate($page); // will return \Abb\Paginator\PaginationResult object
```

Example of using custom count query builder modifier:

```php
<?php

use Abb\Paginator\Adapter\DoctrineDbalAdapter;
use Abb\Paginator\Paginator;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

$params = [/*...*/];
$connection = DriverManager::getConnection($params);

$qb = $connection->createQueryBuilder()
    ->select('p.*')
    ->from('posts', 'p')
    ->where('p.published = true');

$countQbModifier = function (QueryBuilder $qb): void {
   $qb->select('count(distinct p.id) AS cnt')
       ->setMaxResults(1);
};

$adapter = new DoctrineDbalAdapter($qb, $countQbModifier);
$paginator = new Paginator($adapter);
$paginationResult = $paginator->paginate(1);
```
