<?php

namespace diamond\sql\pdo;

use diamond\collection\LinkedQueue;
use diamond\sql\Connection;
use diamond\sql\ResultSet;
use diamond\sql\SqlException;
use diamond\sql\Statement;
use PDO;
use PDOStatement;

class PDOBaseStatement implements Statement
{
	private $pdoConnection;
	private $className;
	private $results;
	private $fetchSize;
	private $fetchMode;
	private $queryTimeout;
	private $emulatePrepares;

	public function __construct(PDOConnection $pdoConnection)
	{
		$this->results = new LinkedQueue;
		$this->pdoConnection = $pdoConnection;
		$this->driverOptions = new PDODriverOptions();
		$this->fetchMode = $pdoConnection->getDefaultFetchMode();
	}

	// ------------------------- CONNECTION -------------------------

	public function isClosed(): bool
	{
		return $this->pdoConnection->isClosed();
	}

	public function getConnection(): Connection
	{
		return $this->pdoConnection;
	}

	// ------------------------- PREFERENCES -------------------------

	public function getFetchSize(): ?int
	{
		return $this->fetchSize;
	}

	public function setFetchSize(?int $fetchSize): void
	{
		$this->fetchSize = $fetchSize;
	}

	public function getQueryTimeout(): ?int
	{
		return $this->queryTimeout;
	}

	public function setQueryTimeout(?int $queryTimeout): void
	{
		$this->queryTimeout = $queryTimeout;
	}

	public function getFetchMode(): ?int
	{
		return $this->fetchMode;
	}

	public function setFetchMode(?int $fetchMode): void
	{
		$this->fetchMode = $fetchMode;
	}

	protected function getClassName(): ?string
	{
		return $this->className;
	}

	public function setClassName(?string $class_name): void
	{
		if ($class_name !== null && !class_exists($class_name))
			throw new SqlException(SqlException::INVALID_CLASS_NAME);

		$this->className = $class_name;
	}

	/* TODO
	public function getMaxRows(): int;
	public function setMaxRows(int $maxRows): void;
	public function getFetchDirection(): int;
	public function setFetchDirection(int $direction): void;
	public function getWarnings(): SqlWarning;
	public function clearWarnings(): void;
	*/

	// ------------------------- EXECUTENERS -------------------------

	protected function verifyConnection(): void
	{
		if ($this->pdoConnection === null || $this->results === null)
			throw new SqlException(SqlException::CLOSED_STATEMENT);

		if ($this->pdoConnection->isClosed())
			throw new SqlException(SqlException::CLOSED);
	}

	protected function newPdoStatement(string $statement): PDOStatement
	{
		$pdoStatement = $this->pdoConnection->getPDO()->prepare($statement, $this->pdoConnection->getDriverOptions()->toArray());

		// FIXME NOT FUCKIN WORK if ($this->getFetchMode()) $pdoStatement->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $this->getFetchMode());
		if ($this->getFetchSize()) $pdoStatement->setAttribute(PDO::ATTR_PREFETCH, $this->getFetchSize());
		if ($this->getQueryTimeout()) $pdoStatement->setAttribute(PDO::ATTR_TIMEOUT, $this->getQueryTimeout());

		return $pdoStatement;
	}

	public function executeSql(string $sql): bool
	{
		$this->verifyConnection();

		if (!($pdoStatement = $this->newPdoStatement($sql))->execute())
			return false;

		$factory = new PDOResultSetFactory();
		$builder = $factory->newPDOResultSetBuilder();
		$result = $builder->newFetch($pdoStatement, $this->getFetchMode(), $this->getClassName());

		return $this->results->add($result);
	}

	public function executeQuerySql(string $sql): ResultSet
	{
		$this->verifyConnection();

		if (!($pdoStatement = $this->newPdoStatement($sql))->execute())
			throw new SqlException(SqlException::EXECUTE_QUERY, $sql);

		$factory = new PDOResultSetFactory();
		$builder = $factory->newPDOResultSetBuilder();
		$result = $builder->newFetch($pdoStatement, $this->getFetchMode(), $this->getClassName());

		return $result;
	}

	public function executeUpdateSql(string $sql): ?int
	{
		$this->verifyConnection();

		if (!($pdoStatement = $this->newPdoStatement($sql))->execute())
			return null;

		return $pdoStatement->rowCount();
	}

	public function getResultSet(): ResultSet
	{
		$this->verifyConnection();

		if ($this->results->isEmpty())
			throw new SqlException(SqlException::NO_RESULTS);

		return $this->results->peek();
	}

	public function close(): void
	{
		$this->__destruct();
	}

	public function __destruct()
	{
		if ($this->results !== null)
		{
			$this->results->clear();
			$this->results = null;
		}

		if ($this->pdoConnection !== null)
			$this->pdoConnectios = null;
	}
}

