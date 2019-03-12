<?php

namespace test\diamond\sql;

use PHPUnit\Framework\TestSuite;

/**
 * Static test suite.
 */
class SqlSuite extends TestSuite
{
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct()
	{
		$this->setName('SqlSuite');
		$this->addTest(new MysqlConnectionTest);
	}

	/**
	 * Creates the suite.
	 */
	public static function suite()
	{
		return new self();
	}
}

