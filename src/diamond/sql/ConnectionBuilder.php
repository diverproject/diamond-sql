<?php

namespace diamond\sql;

interface ConnectionBuilder
{
	public function newMysqlConnection(string $host, string $username, string $password, string $schema): Connection;

	/* TODO
	public function newSqlServerConnection(string $host, string $username, string $password, string $schema): Connection;
	public function newPostgreConnection(string $host, string $username, string $password, string $schema): Connection;
	public function newMongoConnection(string $host, string $username, string $password, string $schema): Connection;
	public function newOracleConnection(string $host, string $username, string $password, string $schema): Connection;
	*/
}

