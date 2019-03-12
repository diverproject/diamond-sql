<?php

namespace diamond\sql\pdo;

use DateTime;
use diamond\lang\BoolParser;
use diamond\lang\FloatParser;
use diamond\lang\IntParser;
use diamond\lang\StringParser;
use diamond\sql\CallableStatement;
use diamond\sql\SqlException;
use diamond\sql\Time;

class PDOCallableStatement extends PDOPreparedStatement implements CallableStatement
{
	public const OUTPUT_REGEX = '/(?<parameters>\@[a-zA-Z0-9_]+)/';

	private $output;

	public function __construct(PDOConnection $pdoConnection, string $sql)
	{
		parent::__construct($pdoConnection, $sql);
	}

	// ------------------------- EXECUTENERS -------------------------

	public function execute(): bool
	{
		$this->parameterOffset = 0;
		$executed = ($this->newPdoStatementParametrized())->execute();

		if (!$executed)
			return false;

		$sql = $this->getSql();
		$matches = [];

		if (!StringParser::contains($sql, 'SELECT') && preg_match_all(self::OUTPUT_REGEX, $sql, $matches))
		{
			foreach ($matches['parameters'] as &$parameter)
				$parameter = format('%s `%s`', $parameter, substr($parameter, 1));

			$sql = format('SELECT %s', implode(', ', $matches['parameters']));
			$statement = $this->getConnection()->prepareStatement($sql);
			$statement->setFetchMode(self::FETCH_BOTH);
			$result = $statement->executeQuery();
			$this->output = $result->current();
			$result->close();
			$result = null;
		}

		return $executed;
	}

	// ------------------------- BASE GETTERS -------------------------

	protected function getOutput(string $parameter)
	{
		$parameter -= 1;

		if (!array_key_exists($parameter, $this->output))
			throw new SqlException(SqlException::OUTPUT_INDEX, $parameter);

		return $this->output[$parameter];
	}

	public function getBool(string $parameter): ?bool
	{
		return is_null(($output = $this->getOutput($parameter))) ? null : BoolParser::parseBool($output);
	}

	public function getByte(string $parameter): ?int
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (PDOUtil::isByte(($int = IntParser::parseInteger($output))))
			return $int;

		throw new SqlException(SqlException::BYTE_PARSE_GET, $parameter);
	}

	public function getShort(string $parameter): ?int
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (PDOUtil::isShort(($int = IntParser::parseInteger($output))))
			return $int;

		throw new SqlException(SqlException::SHORT_PARSE_GET, $parameter);
	}

	public function getInt(string $parameter): ?int
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (PDOUtil::isInteger(($int = IntParser::parseInteger($output))))
			return $int;

		throw new SqlException(SqlException::INT_PARSE_GET, $parameter);
	}

	public function getLong(string $parameter): ?int
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (PDOUtil::isLong(($int = IntParser::parseInteger($output))))
			return $int;

		throw new SqlException(SqlException::LONG_PARSE_GET, $parameter);
	}

	public function getFloat(string $parameter): ?float
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (PDOUtil::isFloat(($float = FloatParser::parseFloat($output))))
			return $float;

		throw new SqlException(SqlException::FLOAT_PARSE_GET, $parameter);
	}

	public function getDouble(string $parameter): ?float
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (PDOUtil::isDouble(($float = FloatParser::parseFloat($output))))
			return $float;

		throw new SqlException(SqlException::DOUBLE_PARSE_GET, $parameter);
	}

	public function getChar(string $parameter): ?string
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (strlen($output) === 1)
			return $output;

		throw new SqlException(SqlException::CHAR_PARSE_GET, $parameter);
	}

	public function getString(string $parameter): ?string
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		return $output;
	}

	public function getTime(string $parameter): ?Time
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (($time = IntParser::parseInteger($output, false)) !== null)
			return new Time($time);

		$time = new Time();

		if ($time->setFullTimeFormat($output));
			return $time;

		throw new SqlException(SqlException::TIME_PARSE_GET, $parameter);
	}

	public function getDate(string $parameter): ?DateTime
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (($timestamp = IntParser::parseInteger($output, false)) !== null)
			return new DateTime(date(PDOUtil::DATETIME_FORMAT, $timestamp));

		if (PDOUtil::isDate($output))
			return new DateTime($output);

		throw new SqlException(SqlException::DATE_PARSE_GET, $parameter);
	}

	public function getDateTime(string $parameter): ?DateTime
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (($timestamp = IntParser::parseInteger($output, false)) !== null)
			return new DateTime(date(PDOUtil::DATETIME_FORMAT, $timestamp));

		if (PDOUtil::isDateTime($output))
			return new DateTime($output);

		throw new SqlException(SqlException::DATETIME_PARSE_GET, $parameter);
	}

	public function getTimestamp(string $parameter): ?DateTime
	{
		if (is_null(($output = $this->getOutput($parameter))))
			return null;

		if (($timestamp = IntParser::parseInteger($output, false)) !== null)
			return new DateTime(date(PDOUtil::DATETIME_FORMAT, $timestamp));

		if (PDOUtil::isDateTime($output))
			return new DateTime($output);

		throw new SqlException(SqlException::TIMESTAMP_PARSE_GET, $parameter);
	}

	// ------------------------- SEQUENCIAL GETTERS -------------------------

	public function getBoolSequencial(): ?bool
	{
		return $this->getBool($this->increaseParameterOffset());
	}

	public function getByteSequencial(): ?int
	{
		return $this->getByte($this->increaseParameterOffset());
	}

	public function getShortSequencial(): ?int
	{
		return $this->getShort($this->increaseParameterOffset());
	}

	public function getIntSequencial(): ?int
	{
		return $this->getInt($this->increaseParameterOffset());
	}

	public function getLongSequencial(): ?int
	{
		return $this->getLong($this->increaseParameterOffset());
	}

	public function getFloatSequencial(): ?float
	{
		return $this->getFloat($this->increaseParameterOffset());
	}

	public function getDoubleSequencial(): ?float
	{
		return $this->getDouble($this->increaseParameterOffset());
	}

	public function getCharSequencial(): ?string
	{
		return $this->getChar($this->increaseParameterOffset());
	}

	public function getStringSequencial(): ?string
	{
		return $this->getString($this->increaseParameterOffset());
	}

	public function getTimeSequencial(): ?Time
	{
		return $this->getTime($this->increaseParameterOffset());
	}

	public function getDateSequencial(): ?DateTime
	{
		return $this->getDate($this->increaseParameterOffset());
	}

	public function getDateTimeSequencial(): ?DateTime
	{
		return $this->getDateTime($this->increaseParameterOffset());
	}

	public function getTimestampSequencial(): ?DateTime
	{
		return $this->getTimestamp($this->increaseParameterOffset());
	}

	// ------------------------- ADVANCED GETTERS -------------------------

	public function getArray(): array
	{
		$outputs = [];

		foreach ($this->parameters as $output)
			if ($output instanceof PDOParameter)
			{
				$variable = null;

				switch ($output->getDataType())
				{
					case PDOParameter::DATA_TYPE_BOOL: $variable = $this->getBool($output->getName()); break;
					case PDOParameter::DATA_TYPE_BYTE: $variable = $this->getByte($output->getName()); break;
					case PDOParameter::DATA_TYPE_SHORT: $variable = $this->getShort($output->getName()); break;
					case PDOParameter::DATA_TYPE_INT: $variable = $this->getInt($output->getName()); break;
					case PDOParameter::DATA_TYPE_LONG: $variable = $this->getLong($output->getName()); break;
					case PDOParameter::DATA_TYPE_FLOAT: $variable = $this->getFloat($output->getName()); break;
					case PDOParameter::DATA_TYPE_DOUBLE: $variable = $this->getDouble($output->getName()); break;
					case PDOParameter::DATA_TYPE_STRING: $variable = $this->getString($output->getName()); break;
					case PDOParameter::DATA_TYPE_TIME: $variable = $this->getTime($output->getName()); break;
					case PDOParameter::DATA_TYPE_DATE: $variable = $this->getDate($output->getName()); break;
					case PDOParameter::DATA_TYPE_DATETIME: $variable = $this->getDateTime($output->getName()); break;
					case PDOParameter::DATA_TYPE_TIMESTAMP: $variable = $this->getTimestamp($output->getName()); break;
//					case PDOParameter::DATA_TYPE_BYTES: $variable = $this->getBytes($output->getName()); break; TODO
				}

				$outputs[$output->getName()] = $variable;
			}

		return $outputs;
	}

	public function getObject(string $parameter): object
	{
		$array = $this->getArray($parameter);
		$class_name = isset($array['class_name']) ? $array['class_name'] : $array[0];

		if (!class_exists($class_name))
			throw new SqlException(SqlException::OBJECT_CLASS_EXISTS, $parameter, $class_name);

		$object = new $class_name();
		$method_name = 'fromArray';

		if (!method_exists($object, $method_name))
			throw new SqlException(SqlException::OBJECT_METHOD_EXISTS, $class_name, $parameter);

		$object->$method_name($array);

		return $object;
	}

	public function getObjectClassName(string $parameter, ?string $class_name): object
	{
		$array = $this->getArray($parameter);

		if (!class_exists($class_name))
			throw new SqlException(SqlException::OBJECT_CLASS_EXISTS, $parameter, $class_name);

		$object = new $class_name();
		$method_name = 'fromArray';

		if (!method_exists($object, $method_name))
			throw new SqlException(SqlException::OBJECT_METHOD_EXISTS, $class_name, $parameter);

		$object->$method_name($array);

		return $object;
	}

	public function getObjectExistent(string $parameter, object $object): void
	{
		$array = $this->getArray($parameter);
		$method_name = 'fromArray';

		if (!method_exists($object, $method_name))
			throw new SqlException(SqlException::OBJECT_METHOD_EXISTS, get_class($object), $parameter);

		$object->$method_name($array);
	}

	public function getBlob(string $parameter): string
	{
		$data = $this->getOutput($parameter);

		if (!is_string($data))
			throw new SqlException(SqlException::BLOB_PARSE_GET, gettype($data), $parameter);

		return $data;
	}

	public function getBlobSequencial(): string
	{
		return $this->getBlob($this->increaseParameterOffset());
	}
}

