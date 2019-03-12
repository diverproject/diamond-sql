<?php

declare(strict_types=1);

namespace test\diamond\sql;

use PHPUnit\Framework\TestCase;
use diamond\lang\Diamond;
use diamond\lang\System;
use diamond\lang\utils\GlobalFunctions;

/**
 * @see TestCase
 * @author Andrew
 */
abstract class DiamondSqlTest extends TestCase
{
	public const HOST = 'localhost';
	public const USERNAME = 'root';
	public const PASSWORD = 'root';
	public const SCHEMA = 'DiamondTest';
	public const SCHEMA2 = 'employees';

	/**
	 * @var string mensagem para <b>arquitetura não encontrada</b>
	 */
	protected const ARCHITECTURE_NOT_FOUND = 'não foi possível reconhecer a arquitetura do sistema';

	/**
	 * @var int código da arquitetura do sistema.
	 */
	protected $architecture;

	/**
	 *
	 */
	public function __construct()
	{
		GlobalFunctions::load();
		Diamond::setEnvironment(Diamond::ENVIRONMENT_TEST_CASE);
		Diamond::setEnabledParseThrows(false);

		parent::__construct(nameOf($this));

		$this->architecture = System::getArchitecture();
	}
}

