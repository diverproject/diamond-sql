<?php

namespace diamond\sql;

use diamond\json\JsonObject;

class RawResultSet extends AbstractResultSet
{
	private $entries;

	public function __construct()
	{
		$this->entries = [];
	}

	public function getEntries(): array
	{
		return $this->entries;
	}

	public function setEntries(array $entries)
	{
		$this->entries = array_values($entries);
	}

	public function close(): void
	{
		$this->entries = null;
		$this->setEntries([]);
	}

	public function rewind(): void
	{
		reset($this->entries);
	}

	public function current()
	{
		return ($current = current($this->entries)) === false ? null : $current;
	}

	public function key(): int
	{
		return key($this->entries);
	}

	public function next(): bool
	{
		return next($this->entries) !== false;
	}

	public function absolute(int $row): void
	{
		if (!isset($this->entries[$row]))
			throw new SqlException(SqlException::ROW_NOT_FOUND, $row);

		if ($this->key() > $row)
			while ($this->key() > $row)
				$this->previous();

		else if ($this->key() < $row)
			while ($this->key() < $row)
				$this->next();
	}

	public function getRowCount(): int
	{
		return count($this->entries);
	}

	public function getAffectedRows(): int
	{
		return 0;
	}

	public function getArray(): array
	{
		return $this->current();
	}

	public function getJsonObject(string $class_name): JsonObject
	{
		if (!($object = new $class_name()) instanceof JsonObject)
			throw new SqlException(SqlException::JSON_OBJECT, $class_name);

		$object->fromArray($this->current());

		return $object;
	}

	public function loadJsonObject(JsonObject $jsonObject): void
	{
		$jsonObject->fromArray($this->getArray());
	}
}

