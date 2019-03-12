<?php

namespace diamond\sql;

use DateTime;

interface CallableStatement extends PreparedStatement
{
	// ------------------------- BASE GETTERS -------------------------

	public function getBool(string $parameter): ?bool;
	public function getByte(string $parameter): ?int;
	public function getShort(string $parameter): ?int;
	public function getInt(string $parameter): ?int;
	public function getLong(string $parameter): ?int;
	public function getFloat(string $parameter): ?float;
	public function getDouble(string $parameter): ?float;
	public function getChar(string $parameter): ?string;
	public function getString(string $parameter): ?string;
	public function getTime(string $parameter): ?Time;
	public function getDate(string $parameter): ?DateTime;
	public function getDateTime(string $parameter): ?DateTime;
	public function getTimestamp(string $parameter): ?DateTime;

	// ------------------------- SEQUENCIAL GETTERS -------------------------

	public function getBoolSequencial(): ?bool;
	public function getByteSequencial(): ?int;
	public function getShortSequencial(): ?int;
	public function getIntSequencial(): ?int;
	public function getLongSequencial(): ?int;
	public function getFloatSequencial(): ?float;
	public function getDoubleSequencial(): ?float;
	public function getCharSequencial(): ?string;
	public function getStringSequencial(): ?string;
	public function getTimeSequencial(): ?Time;
	public function getDateSequencial(): ?DateTime;
	public function getDateTimeSequencial(): ?DateTime;
	public function getTimestampSequencial(): ?DateTime;

	// ------------------------- ADVANCED GETTERS -------------------------

	public function getArray(): array;
	public function getObject(string $parameter): object;
	public function getObjectClassName(string $parameter, ?string $class_name): object;
	public function getObjectExistent(string $parameter, object $object): void;

	// ------------------------- STREAM GETTERS -------------------------

	public function getBlob(string $parameter): string;
	public function getBlobSequencial(): string;
}

