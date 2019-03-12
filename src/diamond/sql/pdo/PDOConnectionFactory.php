<?php

namespace diamond\sql\pdo;

use diamond\sql\ConnectionBuilder;
use diamond\sql\ConnectionFactory;

class PDOConnectionFactory implements ConnectionFactory
{
	public function newConnectionBuilder(): ConnectionBuilder
	{
		return new PDOConnectionBuilder();
	}
}

