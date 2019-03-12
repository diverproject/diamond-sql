<?php

namespace diamond\sql\pdo;

class PDOResultSetFactory
{
	public function newPDOResultSetBuilder(): PDOResultSetBuilder
	{
		return new PDOResultSetBuilder();
	}
}

