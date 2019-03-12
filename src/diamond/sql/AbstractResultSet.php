<?php

namespace diamond\sql;

use DateTime;
use diamond\json\JsonUtil;
use diamond\lang\BoolParser;
use diamond\lang\FloatParser;
use diamond\lang\IntParser;
use diamond\sql\pdo\PDOUtil;
use Exception;

abstract class AbstractResultSet implements ResultSet
{
	public function valid(): bool
	{
		return $this->current() !== null;
	}

	private function getColumnData(string $column)
	{
		$current = $this->current();

		if (isset($current[$column]))
			return $current[$column];

		throw new SqlException(SqlException::COLUMN_NOT_FOUND, $column);
	}

	public function getObject(?string $class_name = null): object
	{
		$current = $this->current();

		if ($class_name === null)
			return (object) $current;

		try {
			$object = new $class_name();
		} catch (Exception $e) {
			throw new SqlException(SqlException::OBJECT_INSTANCE, $class_name, $e->getMessage());
		}

		try {
			JsonUtil::parseArray((array) $current, $object);
		} catch (Exception $e) {
			throw new SqlException(SqlException::OBJECT_PARSE_GET, $class_name);
		}
	}

	public function getBool(string $column): ?bool
	{
		return strval($this->getColumnData($column)) !== '0';
	}

	public function getString(string $column): ?string
	{
		return strval($this->getColumnData($column));
	}

	public function getBytes(string $column): ?string
	{
		return $this->getString($column);
	}

	public function getByte(string $column): ?int
	{
		if (PDOUtil::isByte($long = $this->getLong($column)))
			return $long;

		throw new SqlException(SqlException::BYTE_PARSE_GET, $long, $column);
	}

	public function getShort(string $column): ?int
	{
		if (PDOUtil::isShort($long = $this->getLong($column)))
			return $long;

		throw new SqlException(SqlException::SHORT_PARSE_GET, $long, $column);
	}

	public function getInt(string $column): ?int
	{
		if (PDOUtil::isInteger($long = $this->getLong($column)))
				return $long;

		throw new SqlException(SqlException::INT_PARSE_GET, $long, $column);
	}

	public function getLong(string $column): ?int
	{
		if (($data = $this->getColumnData($column)) === null)
			return null;

		if (($int = IntParser::parseInteger($data, false)) !== null)
			return $int;

		throw new SqlException(SqlException::LONG_PARSE_GET, $data, $column);
	}

	public function getFloat(string $column): ?float
	{
		if (PDOUtil::isFloat($double = $this->getDouble($column)))
			return $double;

		throw new SqlException(SqlException::FLOAT_PARSE_GET, $double, $column);
	}

	public function getDouble(string $column): ?float
	{
		if (($data = $this->getColumnData($column)) === null)
			return null;

		if (($float = FloatParser::parseFloat($data, false)) !== null)
			return $float;

		throw new SqlException(SqlException::DOUBLE_PARSE_GET, $data, $column);
	}

	public function getBoolean(string $column): ?bool
	{
		if (($data = $this->getColumnData($column)) === null)
			return null;

		if (($bool = BoolParser::parseBool($data, false)) !== null)
			return $bool;

		throw new SqlException(SqlException::BOOL_PARSE_GET, $data, $column);
	}

	public function getTime(string $column): Time
	{
		$time = new Time();

		if ($time->setFullTimeFormat($data = strval($this->getColumnData($column))))
			return $time;

		throw new SqlException(SqlException::TIME_PARSE_GET, $data, $column);
	}

	public function getTimestamp(string $column): DateTime
	{
		return new DateTime($this->getInt($column));
	}

	public function getDate(string $column): DateTime
	{
		if (($data = $this->getColumnData($column)) === null)
			return null;

		if (!($timestamp = strtotime($data)))
			throw new SqlException(SqlException::DATE_PARSE_GET, $data, $column);

		$datetime = new DateTime();
		$datetime->setTimestamp($timestamp);

		return $datetime;
	}

	public function getDateTime(string $column): ?DateTime
	{
		if (($data = $this->getColumnData($column)) === null)
			return null;

		$datetime = new DateTime();

		if (IntParser::isInteger($data))
			$datetime->setTimestamp($data);
		else if (($timestamp = strtotime($data)) !== false)
			$datetime->setTimestamp($timestamp);

		throw new SqlException(SqlException::DATETIME_PARSE_GET, $data, $column);
	}

	public function getBlob(string $column)
	{
		if (($data = $this->getColumnData($column)) === null)
			return null;

		if (!is_resource($data))
			throw new SqlException(SqlException::BLOB_PARSE_GET, gettype($data), $column);

		return $data;
	}

	public function relative(int $row): void
	{
		if ($row > 0)
			$this->absolute($this->key() + $row);

		else if ($row < 0)
			$this->absolute($this->key() - $row);
	}

	public function first(): void
	{
		$this->absolute(0);
	}

	public function last(): void
	{
		$this->absolute($this->getRowCount() - 1);
	}

	public function previous(): bool
	{
		if ($this->isFirst())
			return false;

		$this->relative(-1);
		return true;
	}

	public function isFirst(): bool
	{
		return $this->key() === 0;
	}

	public function isLast(): bool
	{
		return $this->key() === $this->getRowCount() - 1;
	}

	public function getRow(): int
	{
		return $this->key();
	}
}

