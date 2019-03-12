<?php

namespace diamond\sql;

use PDO;

interface Statement extends AutoCloseable
{
	public const FETCH_BOTH = PDO::FETCH_BOTH;
	public const FETCH_INDEX = PDO::FETCH_NUM;
	public const FETCH_COLUMN = PDO::FETCH_ASSOC;
	public const FETCH_OBJECT = PDO::FETCH_OBJ;
	public const FETCH_CLASS = PDO::FETCH_CLASS;

	// ------------------------- CONNECTION -------------------------

	public function isClosed(): bool;
	public function getConnection(): Connection;

	// ------------------------- PREFERENCES -------------------------

	public function getFetchMode(): ?int;
	public function setFetchMode(?int $fetchMode): void;
	public function getFetchSize(): ?int;
	public function setFetchSize(?int $fetchSize): void;
	public function getQueryTimeout(): ?int;
	public function setQueryTimeout(?int $queryTimeout): void;
	public function setClassName(string $class_name): void;

	// ------------------------- EXECUTENERS -------------------------

	public function executeSql(string $sql): bool;
	public function executeQuerySql(string $sql): ResultSet;
	public function executeUpdateSql(string $sql): ?int;
	public function getResultSet(): ResultSet;
}

