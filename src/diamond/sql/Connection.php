<?php

namespace diamond\sql;

interface Connection extends AutoCloseable
{
	// ------------------------- Connection -------------------------

	public function getHost(): string;
	public function getUsername(): string;
	public function getPort(): int;
	public function getSchema(): string;
	public function setSchema(string $schema): void;
	public function getCharset(): ?string;
	public function getMetaData(): MetaData;
	public function getDefaultFetchMode(): int;
	public function getDefaultFetchSize(): int;

	// ------------------------- Control -------------------------

	public function connect(): void;
	public function isClosed(): bool;
	public function setAutoCommit(bool $autoCommit): void;
	public function isAutoCommit(): bool;
	public function beginTransaction(): void;
	public function commit(): void;
	public function rollback(): void;

	// ------------------------- Statement -------------------------

	public function createStatement(): Statement;
	public function prepareStatement(string $sql): PreparedStatement;
	public function prepareCall(string $sql): CallableStatement;

	/* TODO
	// ------------------------- Save Point -------------------------

	public function savePoint(): SavePoint;
	public function setSavePoint(string $name): SavePoint;
	public function rollbackSavePoint(SavePoint $savePoint): void;
	public function releaseSavepoint(SavePoint $savePoint): void;
	*/
}

