<?php

namespace diamond\sql\pdo;

use diamond\json\JsonObject;
use diamond\sql\MetaData;
use PDO;

class PDOMetaData extends JsonObject implements MetaData
{
	private $pdoConnection;

	public function __construct(PDOConnection $pdoConnection)
	{
		$this->pdoConnection = $pdoConnection;
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_DRIVER_NAME"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getDriverName()
	 */
	public function getDriverName(): string
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_SERVER_INFO"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getServerInfo()
	 */
	public function getServerInfo(): string
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_SERVER_INFO);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_SERVER_VERSION"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getServerVersion()
	 */
	public function getServerVersion(): string
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_SERVER_VERSION);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_CLIENT_VERSION"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getClientVersion()
	 */
	public function getClientVersion(): string
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_CLIENT_VERSION);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_CONNECTION_STATUS"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getConnectionStatus()
	 */
	public function getConnectionStatus(): string
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_CONNECTION_STATUS);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_PERSISTENT"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::isPersistent()
	 */
	public function isPersistent(): bool
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_PERSISTENT);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_CASE"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getCase()
	 */
	public function getCase(): int
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_CASE);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_ORACLE_NULLS"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getOracleNulls()
	 */
	public function getOracleNulls(): int
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_ORACLE_NULLS);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_PREFETCH"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getPreFetch()
	 */
	public function getPreFetch(): int
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_PREFETCH);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_ERRMODE"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getErrorMode()
	 */
	public function getErrorMode(): int
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_ERRMODE);
	}

	/**
	 * @JsonAnnotation({"name":"ATTR_TIMEOUT"})
	 * {@inheritDoc}
	 * @see \diamond\sql\MetaData::getTimeout()
	 */
	public function getTimeout(): int
	{
		return $this->pdoConnection->getPDO()->getAttribute(PDO::ATTR_TIMEOUT);
	}
}

