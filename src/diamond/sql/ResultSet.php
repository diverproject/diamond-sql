<?php

namespace diamond\sql;

use DateTime;
use diamond\json\JsonObject;
use Iterator;

interface ResultSet extends Iterator
{
	// ------------------------- CONTROL METHODS -------------------------

	public function close(): void;
	public function current();
	public function next(): bool;
	public function key(): int;
	public function valid(): bool;
	public function rewind(): void;
	public function absolute(int $row): void;
	public function relative(int $row): void;
	public function first(): void;
	public function last(): void;
	public function previous(): bool;
	public function isFirst(): bool;
	public function isLast(): bool;
	public function getRow(): int;
	public function getRowCount(): int;
	public function getAffectedRows(): int;

	// ------------------------- BASE GETTERS -------------------------

	public function getBool(string $column): ?bool;
	public function getByte(string $column): ?int;
	public function getShort(string $column): ?int;
	public function getInt(string $column): ?int;
	public function getLong(string $column): ?int;
	public function getFloat(string $column): ?float;
	public function getDouble(string $column): ?float;
	public function getString(string $column): ?string;
	public function getBytes(string $column): ?string;
	public function getTime(string $column): ?Time;
	public function getDate(string $column): ?DateTime;
	public function getDateTime(string $column): ?DateTime;
	public function getTimestamp(string $column): ?DateTime;
	public function getArray(): array;
	public function getObject(?string $class_name): object;
	public function getJsonObject(string $class_name): JsonObject;
	public function loadJsonObject(JsonObject $jsonObject): void;

	// ------------------------- STREAM GETTERS -------------------------

	public function getBlob(string $column);
}

