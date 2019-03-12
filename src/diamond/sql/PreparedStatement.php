<?php

namespace diamond\sql;

use DateTime;
use diamond\json\JsonObject;

interface PreparedStatement extends Statement
{
	// ------------------------- EXECUTENERS -------------------------

	public function execute(): bool;
	public function executeQuery(): ResultSet;
	public function executeUpdate(): int;

	// ------------------------- BASE SETTERS -------------------------

	public function clearParameters();
	public function setNull(string $parameter): void;
	public function setBool(string $parameter, ?bool $var): void;
	public function setByte(string $parameter, ?int $var): void;
	public function setShort(string $parameter, ?int $var): void;
	public function setInt(string $parameter, ?int $var): void;
	public function setLong(string $parameter, ?int $var): void;
	public function setFloat(string $parameter, ?float $var): void;
	public function setDouble(string $parameter, ?float $var): void;
	public function setUnsafeFloat(string $parameter, $var): void;
	public function setUnsafeDouble(string $parameter, $var): void;
	public function setChar(string $parameter, ?string $var): void;
	public function setString(string $parameter, ?string $var): void;
	public function setBytes(string $parameter, ?string $var): void;
	public function setTime(string $parameter, ?Time $var): void;
	public function setDate(string $parameter, ?DateTime $var): void;
	public function setDateTime(string $parameter, ?DateTime $var): void;
	public function setTimestamp(string $parameter, ?DateTime $var): void;
	public function setArray(int $parameter, array $var): void;
	public function setObject(int $parameter, object $object): void;
	public function setJsonObject(JsonObject $jsonObject, int $export = 0): void;

	// ------------------------- SEQUENCIAL SETTERS -------------------------

	public function addNull(): void;
	public function addBool(?bool $var): void;
	public function addByte(?int $var): void;
	public function addShort(?int $var): void;
	public function addInt(?int $var): void;
	public function addLong(?int $var): void;
	public function addFloat(?float $var): void;
	public function addDouble(?float $var): void;
	public function addChar(?string $var): void;
	public function addString(?string $var): void;
	public function addBytes(?string $var): void;
	public function addTime(?Time $var): void;
	public function addDate(?DateTime $var): void;
	public function addDateTime(?DateTime $var): void;
	public function addTimestamp(?DateTime $var): void;
	public function addArray(array $var): void;
	public function addObject(object $object): void;

	// ------------------------- STREAM SETTERS -------------------------

	public function setBlob(string $parameter, $var): void;
	public function addBlob($var): void;
}

