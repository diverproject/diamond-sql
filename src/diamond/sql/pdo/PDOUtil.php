<?php

namespace diamond\sql\pdo;

use diamond\lang\FloatParser;
use diamond\lang\IntParser;

class PDOUtil
{
	public const TIME_FORMAT = 'H:i:s';
	public const DATE_FORMAT = 'Y-m-d';
	public const DATETIME_FORMAT = 'Y-m-d H:i:s';
	public const DATE_PATTERN = '/^(\d{4})-((0[1-9])|(1[0-2]))-(0[1-9]|[12][0-9]|3[01])$/';
	public const DATETIME_PATTERN = '/^(\d{4})-((0[1-9])|(1[0-2]))-(0[1-9]|[12][0-9]|3[01])\ ([01][0-9]|[2][0-3])\:([0-5][0-9])\:([0-5][0-9])$/';

	public const MIN_BYTE = -128;
	public const MAX_BYTE = 127;
	public const MIN_SHORT = -32768;
	public const MAX_SHORT = 32767;
	public const MIN_INT = -2147483648;
	public const MAX_INT = 2147483647;
	public const MIN_LONG = IntParser::MIN_INTEGER_64;
	public const MAX_LONG = IntParser::MAX_INTEGER_64;
	public const MIN_FLOAT = -9.99991;
	public const MAX_FLOAT = 999999;
	public const MIN_DOUBLE = '-9.99999999999999';
	public const MAX_DOUBLE = '9.99999999999999';

	public static function isByte(int $var): bool
	{
		return IntParser::hasBetween($var, self::MIN_BYTE, self::MAX_BYTE);
	}

	public static function isShort(int $var): bool
	{
		return IntParser::hasBetween($var, self::MIN_SHORT, self::MAX_SHORT);
	}

	public static function isInteger(int $var): bool
	{
		return IntParser::hasBetween($var, self::MIN_INT, self::MAX_INT);
	}

	public static function isLong($var): bool
	{
		return IntParser::isInteger($var) && IntParser::hasBetween($var, self::MIN_LONG, self::MAX_LONG);
	}

	public static function isFloat(float $var): bool
	{
		return FloatParser::hasBetween($var, self::MIN_FLOAT, self::MAX_FLOAT);
	}

	public static function isDouble($var): bool
	{
		return FloatParser::isFloat($var) && FloatParser::hasBetween($var, self::MIN_DOUBLE, self::MAX_DOUBLE);
	}

	public static function isDate(string $date): bool
	{
		return preg_match(self::DATE_PATTERN, $date) === 1;
	}

	public static function isDateTime(string $datetime): bool
	{
		return preg_match(self::DATETIME_PATTERN, $datetime) === 1;
	}
}

