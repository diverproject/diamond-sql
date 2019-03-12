<?php

namespace diamond\sql\pdo;

use diamond\sql\Connection;
use diamond\sql\ConnectionBuilder;
use diamond\sql\pdo\mysql\MysqlPDOConnection;

class PDOConnectionBuilder implements ConnectionBuilder
{
	public const DEFAULT_MYSQL_PORT = 3306;
	public const DEFAULT_CHARSET = 'utf8';

	private $charset;

	public function getCharset(): ?string
	{
		return $this->charset;
	}

	public function setCharset(?string $charset): void
	{
		$this->charset = $charset;
	}

	public function newMysqlConnection(string $host, string $username, string $password, string $schema, ?int $port = null): Connection
	{
		$port = nvl($port, self::DEFAULT_MYSQL_PORT);
		$charset = nvl($this->charset, self::DEFAULT_CHARSET);
		$options = new PDOOptions();

		$connection = new MysqlPDOConnection($host, $username, $password, $schema, $port, $options);
		$connection->setCharset($charset);

		return $connection;
	}
}

