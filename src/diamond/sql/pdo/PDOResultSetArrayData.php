<?php

namespace diamond\sql\pdo;

use diamond\json\JsonUtil;
use diamond\sql\RawResultSet;
use diamond\sql\SqlException;
use PDOStatement;

class PDOResultSetArrayData extends RawResultSet
{
	private $pdoStatement;

	public function __construct(PDOStatement $pdoStatement)
	{
		$this->pdoStatement = $pdoStatement;
	}

	public function close(): void
	{
		if (!$this->pdoStatement->closeCursor())
			throw new SqlException(SqlException::CLOSE_CURSOR);

		$this->pdoStatement = null;
	}

	public function getObject(?string $class_name = null): object
	{
		$array = $this->getArray();

		if ($class_name === null)
		{
			if (!isset($array[0]) && !isset($array['class_name']))
				throw new SqlException(SqlException::OBJECT_CLASS_NAME);

			$class_name = isset($array[0]) ? $array[0] : $array['class_name'];
		}

		$object = new $class_name();
		JsonUtil::parseArray($this->getArray(), $object);

		return $object;
	}
}

