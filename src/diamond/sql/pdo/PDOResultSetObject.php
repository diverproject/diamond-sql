<?php

namespace diamond\sql\pdo;

use diamond\json\JsonExport;
use diamond\json\JsonObject;
use diamond\json\JsonUtil;
use diamond\lang\exceptions\UnsupportedMethodException;
use diamond\sql\RawResultSet;
use diamond\sql\SqlException;
use PDOStatement;

class PDOResultSetObject extends RawResultSet
{
	private $pdoStatement;
	private $class_name;
	private $offset;

	public function __construct(PDOStatement $pdoStatement, ?string $class_name = null)
	{
		parent::__construct();

		$this->pdoStatement = $pdoStatement;
		$this->class_name = $class_name;
		$this->loadEntries();
	}

	private function loadEntries(): void
	{
		$entries = [];

		while ($object = $this->pdoStatement->fetchObject($this->class_name))
			$entries[] = $object;

		$this->setEntries($entries);
	}

	public function close(): void
	{
		parent::close();

		$this->pdoStatement->closeCursor();
		$this->pdoStatement = null;

		if (!$this->pdoStatement->closeCursor())
			throw new SqlException(SqlException::CLOSE_CURSOR);
	}

	public function getArray(): array
	{
		return JsonUtil::parseObject($this->current(), new JsonExport());
	}

	public function getJsonObject(string $class_name): JsonObject
	{
		throw new UnsupportedMethodException();
	}

	public function loadJsonObject(JsonObject $jsonObject): void
	{
		$jsonObject->fromArray($this->getArray());
	}
}

