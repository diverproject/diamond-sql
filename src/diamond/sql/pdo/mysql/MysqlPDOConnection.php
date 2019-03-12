<?php

namespace diamond\sql\pdo\mysql;

use diamond\sql\pdo\PDOConnection;
use PDO;

class MysqlPDOConnection extends PDOConnection
{
	// ------------------------- Connection -------------------------

	public function newDns(): string
	{
		$charset = ternary($this->getCharset() === null, '', format(';charset=%s', $this->getCharset()));

		return format("mysql:host=%s;dbname=%s$charset", $this->getHost(), $this->getSchema());
	}

	// ------------------------- Control -------------------------

	public function onChangeCharset(string $charset): bool
	{
		$statement = $this->getPDO()->prepare("SET character_set_client = ?");
		$statement->bindValue(1, $charset, PDO::PARAM_STR);
		$executed = $statement->execute();
		$statement->closeCursor();

		return $executed;
	}

	public function getConnectionId(): int
	{
		$statement = $this->getPDO()->prepare("SELECT CONNECTION_ID()");

		if ($statement->execute() && $statement->rowCount() === 1)
		{
			$connectionId = $statement->fetchColumn(0);
			$statement->closeCursor();
			return $connectionId;
		}

		$statement->closeCursor();
		return 0;
	}
}

