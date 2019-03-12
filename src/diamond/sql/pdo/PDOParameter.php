<?php

namespace diamond\sql\pdo;

use PDO;

class PDOParameter
{
	public const BIND_COLUMN = 0;
	public const BIND_PARAMETER = 1;
	public const BIND_VALUE = 2;

	public const DATA_TYPE_BOOL = PDO::PARAM_INT;
	public const DATA_TYPE_BYTE = PDO::PARAM_INT;
	public const DATA_TYPE_SHORT = PDO::PARAM_INT;
	public const DATA_TYPE_INT = PDO::PARAM_INT;
	public const DATA_TYPE_LONG = PDO::PARAM_INT;
	public const DATA_TYPE_FLOAT = PDO::PARAM_STR;
	public const DATA_TYPE_DOUBLE = PDO::PARAM_STR;
	public const DATA_TYPE_CHAR = PDO::PARAM_STR;
	public const DATA_TYPE_STRING = PDO::PARAM_STR;
	public const DATA_TYPE_TIME = PDO::PARAM_STR;
	public const DATA_TYPE_DATE = PDO::PARAM_STR;
	public const DATA_TYPE_DATETIME = PDO::PARAM_STR;
	public const DATA_TYPE_TIMESTAMP = PDO::PARAM_STR;
	public const DATA_TYPE_BYTES = PDO::PARAM_LOB;
	public const DATA_TYPE_NULL = PDO::PARAM_NULL;

	private $bindType;
	private $name;
	private $variable;
	private $dataType;
	private $length;
	private $driverOptions;

	public function getBindType(): int
	{
		return $this->bindType;
	}

	public function setBindType(int $bindType): void
	{
		$this->bindType = $bindType;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function &getVariable()
	{
		return $this->variable;
	}

	public function setVariable($variable): void
	{
		$this->variable = $variable;
	}

	public function getDataType(): int
	{
		return $this->dataType;
	}

	public function setDataType(int $dataType): void
	{
		$this->dataType = $dataType;
	}

	public function getLength(): ?int
	{
		return $this->length;
	}

	public function setLength(?int $length): void
	{
		$this->length = $length;
	}

	public function getDriverOptions(): PDODriverOptions
	{
		return $this->driverOptions;
	}

	public function getDriverOptionsArray(): ?array
	{
		return $this->driverOptions === null || count($driver_options = $this->driverOptions->toArray()) === 0 ? null : $driver_options;
	}

	public function setDriverOptions(PDODriverOptions $driverOptions): void
	{
		$this->driverOptions = $driverOptions;
	}
}

