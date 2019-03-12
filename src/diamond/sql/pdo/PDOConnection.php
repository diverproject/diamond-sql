<?php

namespace diamond\sql\pdo;

use diamond\collection\LinkedQueue;
use diamond\lang\BoolParser;
use diamond\lang\StringParser;
use diamond\sql\CallableStatement;
use diamond\sql\Connection;
use diamond\sql\MetaData;
use diamond\sql\PreparedStatement;
use diamond\sql\SqlException;
use diamond\sql\Statement;
use PDO;

abstract class PDOConnection implements Connection
{
	public const DEFAULT_FETCH_MODE = Statement::FETCH_COLUMN;
	public const DEFAULT_FETCH_SIZE = 100;

	private $host;
	private $username;
	private $password;
	private $schema;
	private $port;
	private $charset;
	private $connected;
	private $defaultFetchMode;
	private $defaultFetchSize;
	private $metadata;
	private $driverOptions;
	private $pdo;

	public function __construct(string $host, string $username, string $password, string $schema, ?int $port = null, ?PDOOptions $options = null)
	{
		$this->connected = true;
		$this->errors = new LinkedQueue;

		$this->setHost($host);
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setSchema($schema);
		$this->setPort($port);
	}

	// ------------------------- Connection -------------------------

	public abstract function newDns(): string;

	public function getPDO(): PDO
	{
		if ($this->pdo === null)
			throw new SqlException(SqlException::PDO_CONNECTION);

		return $this->pdo;
	}

	public function getDefaultFetchMode(): int
	{
		return $this->defaultFetchMode;
	}

	public function setDefaultFetchMode(int $defaultFetchMode): void
	{
		$this->defaultFetchMode = $defaultFetchMode;
	}

	public function getDefaultFetchSize(): int
	{
		return $this->defaultFetchSize;
	}

	public function setDefaultFetchSize(int $defaultFetchSize): void
	{
		$this->defaultFetchSize = abs($defaultFetchSize);
	}

	public function getHost(): string
	{
		return $this->host;
	}

	public function setHost(string $host): void
	{
		if (!$this->isClosed())
			throw new SqlException(SqlException::CONNECTED);

		$this->host = $host;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function setUsername(string $username): void
	{
		if (!$this->isClosed())
			throw new SqlException(SqlException::CONNECTED);

		$this->username = $username;
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function setPassword(string $password): void
	{
		if (!$this->isClosed())
			throw new SqlException(SqlException::CONNECTED);

		$this->password = $password;
	}

	public function getSchema(): string
	{
		return $this->schema;
	}

	public function setSchema(string $schema): void
	{
		if (!$this->isClosed())
			throw new SqlException(SqlException::CONNECTED);

		$this->schema = $schema;
	}

	public function getPort(): int
	{
		return $this->port;
	}

	public function setPort(int $port): void
	{
		if (!$this->isClosed())
			throw new SqlException(SqlException::CONNECTED);

		$this->port = $port;
	}

	public function isAutoCommit(): bool
	{
		return BoolParser::parseBool($this->getPDO()->getAttribute(PDO::ATTR_AUTOCOMMIT));
	}

	public function setAutoCommit(bool $autoCommit): void
	{
		if (!$this->getPDO()->setAttribute(PDO::ATTR_AUTOCOMMIT, $autoCommit))
			throw new SqlException(SqlException::AUTO_COMMIT_CHANGE, ternary($autoCommit, 'on', 'off'));
	}

	public function getCharset(): ?string
	{
		return $this->charset;
	}

	public function setCharset(string $charset): void
	{
		if ($this->charset === null)
			$this->charset = $charset;

		else if (!$this->isClosed())
		{
			if (!$this->onChangeCharset($charset))
				throw new SqlException(SqlException::CHARSET_CHANGE, $charset);
			else
				$this->charset = $charset;
		}
	}

	public abstract function onChangeCharset(string $charset): bool;

	public function getMetaData(): MetaData
	{
		return ternary(isset($this->metadata), $this->metadata, ($this->metadata = new PDOMetaData($this)));
	}

	public function getDriverOptions(): PDODriverOptions
	{
		return ternary($this->driverOptions === null, ($this->driverOptions = new PDODriverOptions()), $this->driverOptions);
	}

	public function setDriverOptions(PDODriverOptions $driverOptions): void
	{
		$this->driverOptions = $driverOptions;
	}

	// ------------------------- Control -------------------------

	protected function verifyConnection(): void
	{
		if ($this->isClosed())
			throw new SqlException(SqlException::CLOSED);
	}

	protected function prepareConnection(): void
	{
		if (StringParser::isEmpty($this->getHost()))		throw new SqlException(SqlException::EMPTY_HOST);
		if (StringParser::isEmpty($this->getUsername()))	throw new SqlException(SqlException::EMPTY_USERNAME);
		if (StringParser::isEmpty($this->getSchema()))		throw new SqlException(SqlException::EMPTY_SCHEMA);
		if ($this->getPort() === 0)							throw new SqlException(SqlException::EMPTY_PORT);

		$this->getPDO()->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $this->getDefaultFetchMode());
	}

	public function connect(): void
	{
		if (!$this->isClosed())
			throw new SqlException(SqlException::CONNECTED);

		$this->pdo = new PDO($this->newDns(), $this->getUsername(), $this->getPassword(), $this->getDriverOptions()->toArray());
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
		$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->connected = true;

		$this->setDefaultFetchMode(self::DEFAULT_FETCH_MODE);
		$this->setDefaultFetchSize(self::DEFAULT_FETCH_SIZE);
	}

	public function close(): void
	{
		$this->verifyConnection();
		$this->connected = false;
		$this->__destruct();
	}

	public function isClosed(): bool
	{
		return !$this->connected || $this->pdo === null;
	}

	// ------------------------- Control -------------------------

	public function beginTransaction(): void
	{
		if (!$this->getPDO()->beginTransaction())
			throw new SqlException(SqlException::TRANSACTION_FAILURE);
	}

	public function commit(): void
	{
		if (!$this->getPDO()->commit())
			throw new SqlException(SqlException::COMMIT_FAILURE);
	}

	public function rollback(): void
	{
		if (!$this->getPDO()->rollback())
			throw new SqlException(SqlException::ROLLBACK_FAILURE);
	}

	// ------------------------- Statement -------------------------

	public function createStatement(): Statement
	{
		$this->verifyConnection();
		return new PDOBaseStatement($this);
	}

	public function prepareStatement(string $sql): PreparedStatement
	{
		$this->verifyConnection();
		return new PDOPreparedStatement($this, $sql);
	}

	public function prepareCall(string $sql): CallableStatement
	{
		$this->verifyConnection();
		return new PDOCallableStatement($this, $sql);
	}

	public function getWarning(): array
	{
		$statement = $this->getPDO()->prepare('SHOW WARNINGS');

		if (!$statement->execute())
			throw new SqlException(SqlException::WARNINGS);

		$sqlWarnings = $statement->fetchAll();//[];

		//while ($sqlWarning = $statement->fetchAll())
		//	$sqlWarnings[] = $sqlWarning;

		return $sqlWarnings;
	}

	public function __destruct()
	{
		$this->pdo = null;
	}
}

