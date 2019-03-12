<?php

namespace diamond\sql\pdo;

use diamond\sql\ResultSet;
use diamond\sql\SqlException;
use diamond\sql\Statement;
use PDOStatement;

class PDOResultSetBuilder
{
	public function newFetch(PDOStatement $pdoStatement, int $fetchMode, ?string $class_name): ResultSet
	{
		switch ($fetchMode)
		{
			case Statement::FETCH_BOTH: return $this->newFetchBoth($pdoStatement);
			case Statement::FETCH_INDEX: return $this->newFetchNum($pdoStatement);
			case Statement::FETCH_COLUMN: return $this->newFetchAssoc($pdoStatement);
			case Statement::FETCH_OBJECT: return $this->newFetchObject($pdoStatement, $class_name);
			case Statement::FETCH_CLASS: return $this->newFetchClassName($pdoStatement, $class_name);
		}

		throw new SqlException(SqlException::FETCH_MODE, $pdoStatement->getFetchMode());
	}

	public function newFetchBoth(PDOStatement $pdoStatement): ResultSet
	{
		$entries = $pdoStatement->fetchAll(Statement::FETCH_BOTH);
		$result = new PDOResultSetArrayData($pdoStatement);
		$result->setEntries($entries);

		return $result;
	}

	public function newFetchNum(PDOStatement $pdoStatement): ResultSet
	{
		$entries = $pdoStatement->fetchAll(Statement::FETCH_INDEX);
		$result = new PDOResultSetArrayData($pdoStatement);
		$result->setEntries($entries);

		return $result;
	}

	public function newFetchAssoc(PDOStatement $pdoStatement): ResultSet
	{
		$entries = $pdoStatement->fetchAll(Statement::FETCH_COLUMN);
		$result = new PDOResultSetArrayData($pdoStatement);
		$result->setEntries($entries);

		return $result;
	}

	public function newFetchObject(PDOStatement $pdoStatement, string $class_name): ResultSet
	{
		return new PDOResultSetObject($pdoStatement, $class_name);
	}

	public function newFetchClassName(PDOStatement $pdoStatement, string $class_name): ResultSet
	{
		return new PDOResultSetObject($pdoStatement, $class_name);
	}
}

