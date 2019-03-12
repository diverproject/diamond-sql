<?php

namespace diamond\sql\pdo;

use DateTime;
use diamond\collection\HashTable;
use diamond\json\JsonExport;
use diamond\json\JsonObject;
use diamond\json\JsonUtil;
use diamond\lang\FloatParser;
use diamond\sql\PreparedStatement;
use diamond\sql\ResultSet;
use diamond\sql\SqlException;
use diamond\sql\Time;
use PDOStatement;

class PDOPreparedStatement extends PDOBaseStatement implements PreparedStatement
{
	protected $parameterOffset;
	protected $parameters;
	private $sql;

	public function __construct(PDOConnection $pdoConnection, string $sql)
	{
		parent::__construct($pdoConnection);

		$this->sql = $sql;
		$this->parameters = new HashTable;
		$this->parameterOffset = 0;
		$this->outputs = [];
	}

	public function getSql(): ?string
	{
		return $this->sql;
	}

	protected function setSql(string $sql): void
	{
		$this->sql = $sql;
	}

	// ------------------------- EXECUTENERS -------------------------

	protected function newPdoStatementParametrized(): PDOStatement
	{
		$statement = $this->newPdoStatement($this->getSql());

		foreach ($this->parameters as $pdoParameter)
			if ($pdoParameter instanceof PDOParameter)
				switch ($pdoParameter->getBindType())
				{
					case PDOParameter::BIND_COLUMN:
						$statement->bindColumn(
							$pdoParameter->getName(),
							$pdoParameter->getVariable(),
							$pdoParameter->getDataType(),
							$pdoParameter->getLength(),
							$pdoParameter->getDriverOptionsArray()
						);
						break;

					case PDOParameter::BIND_PARAMETER:
						$statement->bindParam(
							$pdoParameter->getName(),
							$pdoParameter->getVariable(),
							$pdoParameter->getDataType(),
							$pdoParameter->getLength(),
							$pdoParameter->getDriverOptionsArray()
						);
						break;

					case PDOParameter::BIND_VALUE:
						$statement->bindValue(
							$pdoParameter->getName(),
							$pdoParameter->getVariable(),
							PDOParameter::DATA_TYPE_STRING
						);
						break;
				}

		return $statement;
	}

	public function execute(): bool
	{
		return ($this->newPdoStatementParametrized())->execute();
	}

	public function executeQuery(): ResultSet
	{
		if (!(($pdoStatement = $this->newPdoStatementParametrized())->execute()))
			throw new SqlException(SqlException::EXECUTE_QUERY);

		$factory = new PDOResultSetFactory();
		$builder = $factory->newPDOResultSetBuilder();
		$result = $builder->newFetch($pdoStatement, $this->getFetchMode(), $this->getClassName());

		return $result;
	}

	public function executeUpdate(): int
	{
		if (!(($pdoStatement = $this->newPdoStatementParametrized())->execute()))
			throw new SqlException(SqlException::EXECUTE_QUERY);

		return $pdoStatement->rowCount();
	}

	// ------------------------- BASE SETTERS -------------------------

	public function clearParameters()
	{
		$this->parameters->clear();
	}

	protected function setParameter(string $parameter, $var, int $dataType)
	{
		if ($var === null)
			$dataType = PDOParameter::DATA_TYPE_NULL;

		$pdoParameter = new PDOParameter();
		$pdoParameter->setBindType($parameter{0} === ':' ? PDOParameter::BIND_VALUE : PDOParameter::BIND_PARAMETER);
		$pdoParameter->setName($parameter);
		$pdoParameter->setVariable($var);
		$pdoParameter->setDataType($dataType);

		if (!$this->parameters->put($pdoParameter->getName(), $pdoParameter))
			throw new SqlException(SqlException::PARAMETER_INDEX, $parameter);
	}

	public function setNull(string $parameter): void
	{
		$this->setParameter($parameter, null, PDOParameter::DATA_TYPE_STRING);
	}

	public function setBool(string $parameter, ?bool $var): void
	{
		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_BOOL);
	}

	public function setByte(string $parameter, ?int $var): void
	{
		if (!is_null($var) && !PDOUtil::isByte($var))
			throw new SqlException(SqlException::BYTE_PARSE_SET, $var, $parameter);

		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_BYTE);
	}

	public function setShort(string $parameter, ?int $var): void
	{
		if (!is_null($var) && !PDOUtil::isShort($var))
			throw new SqlException(SqlException::SHORT_PARSE_SET, $var, $parameter);

		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_SHORT);
	}

	public function setInt(string $parameter, ?int $var): void
	{
		if (!is_null($var) && !PDOUtil::isInteger($var))
			throw new SqlException(SqlException::INT_PARSE_SET, $var, $parameter);

		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_INT);
	}

	public function setLong(string $parameter, ?int $var): void
	{
		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_LONG);
	}

	public function setFloat(string $parameter, ?float $var): void
	{
		if (!is_null($var) && !PDOUtil::isFloat($var))
			throw new SqlException(SqlException::FLOAT_PARSE_SET, $var, $parameter);

		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_FLOAT);
	}

	public function setDouble(string $parameter, ?float $var): void
	{
		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_DOUBLE);
	}

	public function setUnsafeFloat(string $parameter, $var): void
	{
		if (!is_null($var) && !FloatParser::hasFloatFormat($var))
			throw new SqlException(SqlException::FLOAT_PARSE_SET, $var, $parameter);

		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_FLOAT);
	}

	public function setUnsafeDouble(string $parameter, $var): void
	{
		if (!is_null($var) && !FloatParser::hasFloatFormat($var))
			throw new SqlException(SqlException::DOUBLE_PARSE_SET, $var, $parameter);

		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_DOUBLE);
	}

	public function setChar(string $parameter, ?string $var): void
	{
		if ($var !== null && strlen($var) !== 1)
			throw new SqlException(SqlException::DOUBLE_PARSE_SET, $var, $parameter);

		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_STRING);
	}

	public function setString(string $parameter, ?string $var): void
	{
		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_STRING);
	}

	public function setBytes(string $parameter, ?string $var): void
	{
		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_BYTES);
	}

	public function setTime(string $parameter, ?Time $var): void
	{
		$this->setParameter($parameter, $var === null ? null : $var->getFormatted(), PDOParameter::DATA_TYPE_TIME);
	}

	public function setDate(string $parameter, ?DateTime $var): void
	{
		$this->setParameter($parameter, $var === null ? null : $var->format(PDOUtil::DATE_FORMAT), PDOParameter::DATA_TYPE_DATE);
	}

	public function setDateTime(string $parameter, ?DateTime $var): void
	{
		$this->setParameter($parameter, $var === null ? null : $var->format(PDOUtil::DATETIME_FORMAT), PDOParameter::DATA_TYPE_DATETIME);
	}

	public function setTimestamp(string $parameter, ?DateTime $var): void
	{
		$this->setParameter($parameter, $var === null ? null : $var->format(PDOUtil::DATETIME_FORMAT), PDOParameter::DATA_TYPE_TIMESTAMP);
	}

	public function setArray(int $parameter, array $var): void
	{
		foreach ($var as $value)
		{
			switch (($type = strtolower(gettype($value))))
			{
				case 'null': $this->setNull($value); break;
				case 'string': $this->setString(strval($parameter), $value); break;
				case 'array': $this->setArray($parameter, $value); break;

				case 'bool':
				case 'boolean':
					$this->setBool(strval($parameter), $value);
					break;

				case 'int':
				case 'integer':
					$this->setInt(strval($parameter), $value);
					break;

				case 'float':
				case 'double':
					$this->setFloat(strval($parameter), $value);
					break;

				case 'object':
					if ($value instanceof Time)
						$this->setTime(strval($parameter), $value);
					else if ($value instanceof DateTime)
						$this->setDateTime(strval($parameter), $value);
					else
						throw new SqlException(SqlException::PARAMETER_OBJECT, $parameter, get_class($value));
					break;

				default:
					throw new SqlException(SqlException::PARAMETER_DATA_TYPE, $type);
			}

			$parameter++;
		}
	}

	public function setObject(int $parameter, object $object): void
	{
		$this->setArray($parameter, JsonUtil::parseObject($object, new JsonExport));
	}

	public function setJsonObject(JsonObject $jsonObject, int $export = 0): void
	{
		$this->setArray($this->parameterOffset, $jsonObject->toArray($export));
	}

	// ------------------------- SEQUENCIAL SETTERS -------------------------

	protected function increaseParameterOffset(): string
	{
		return strval(++$this->parameterOffset);
	}

	public function addNull(): void
	{
		$this->setNull($this->increaseParameterOffset());
	}

	public function addBool(?bool $var): void
	{
		$this->setBool($this->increaseParameterOffset(), $var);
	}

	public function addByte(?int $var): void
	{
		$this->setByte($this->increaseParameterOffset(), $var);
	}

	public function addShort(?int $var): void
	{
		$this->setShort($this->increaseParameterOffset(), $var);
	}

	public function addInt(?int $var): void
	{
		$this->setInt($this->increaseParameterOffset(), $var);
	}

	public function addLong(?int $var): void
	{
		$this->setLong($this->increaseParameterOffset(), $var);
	}

	public function addFloat(?float $var): void
	{
		$this->setFloat($this->increaseParameterOffset(), $var);
	}

	public function addDouble(?float $var): void
	{
		$this->setDouble($this->increaseParameterOffset(), $var);
	}

	public function addChar(?string $var): void
	{
		$this->setString($this->increaseParameterOffset(), $var);
	}

	public function addString(?string $var): void
	{
		$this->setString($this->increaseParameterOffset(), $var);
	}

	public function addBytes(?string $var): void
	{
		$this->setBytes($this->increaseParameterOffset(), $var);
	}

	public function addTime(?Time $var): void
	{
		$this->setTime($this->increaseParameterOffset(), $var);
	}

	public function addDate(?DateTime $var): void
	{
		$this->setDate($this->increaseParameterOffset(), $var);
	}

	public function addDateTime(?DateTime $var): void
	{
		$this->setDateTime($this->increaseParameterOffset(), $var);
	}

	public function addTimestamp(?DateTime $var): void
	{
		$this->setTimestamp($this->increaseParameterOffset(), $var);
	}

	public function addArray(array $var): void
	{
		$this->setArray(++$this->parameterOffset, $var);
	}

	public function addObject(object $object): void
	{
		$this->setObject(++$this->parameterOffset, $object);
	}

	// ------------------------- STREAM SETTERS -------------------------

	public function setBlob(string $parameter, $var): void
	{
		if ($var !== null && !is_resource($var))
			throw new SqlException(SqlException::BLOB_PARSE_SET, gettype($var), $parameter);

		$this->setParameter($parameter, $var, PDOParameter::DATA_TYPE_BYTES);
	}

	public function addBlob($var): void
	{
		$this->setBlob($this->increaseParameterOffset(), $var);
	}
}

