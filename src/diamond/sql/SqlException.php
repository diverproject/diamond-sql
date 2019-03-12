<?php

namespace diamond\sql;

use Exception;

class SqlException extends Exception
{
	private static $customMessages = [];

	public const SCHEMA_CHANGE = 1;
	public const AUTO_COMMIT_CHANGE = 2;
	public const CHARSET_CHANGE = 3;
	public const EMPTY_HOST = 4;
	public const EMPTY_USERNAME = 5;
	public const EMPTY_SCHEMA = 6;
	public const EMPTY_PORT = 7;
	public const CONNECTED = 8;
	public const CLOSED = 9;
	public const TRANSACTION_FAILURE = 10;
	public const COMMIT_FAILURE = 11;
	public const ROLLBACK_FAILURE = 11;
	public const FETCH_MODE_CHANGE = 12;
	public const FETCH_SIZE_CHANGE = 13;
	public const QUERY_TIMEOUT_CHANGE = 14;

	public const CLASS_NAME = 101;
	public const NO_RESULTS = 102;
	public const EXECUTE_QUERY = 103;
	public const CLOSED_STATEMENT = 104;

	public const PARAMETER_DATA_TYPE = 201;
	public const PDO_CONNECTION = 202;
	public const COLUMN_NOT_FOUND = 203;
	public const OBJECT_INSTANCE = 204;
	public const OBJECT_PARSE = 205;
	public const FETCH_MODE = 206;
	public const ROW_NOT_FOUND = 207;
	public const JSON_OBJECT = 208;
	public const OBJECT_CLASS_NAME = 209;
	public const PARAMETER_INDEX = 210;
	public const WARNINGS = 211;
	public const PARAMETER_OBJECT = 212;
	public const CLOSE_CURSOR = 213;

	public const BOOL_PARSE_GET = 301;
	public const BYTE_PARSE_GET = 302;
	public const SHORT_PARSE_GET = 303;
	public const INT_PARSE_GET = 304;
	public const LONG_PARSE_GET = 305;
	public const FLOAT_PARSE_GET = 306;
	public const DOUBLE_PARSE_GET = 307;
	public const CHAR_PARSE_GET = 308;
	public const TIME_PARSE_GET = 309;
	public const DATE_PARSE_GET = 310;
	public const DATETIME_PARSE_GET = 311;
	public const TIMESTAMP_PARSE_GET = 312;
	public const BLOB_PARSE_GET = 313;

	public const BOOL_PARSE_SET = 351;
	public const BYTE_PARSE_SET = 352;
	public const SHORT_PARSE_SET = 353;
	public const INT_PARSE_SET = 354;
	public const LONG_PARSE_SET = 355;
	public const FLOAT_PARSE_SET = 356;
	public const DOUBLE_PARSE_SET = 357;
	public const CHAR_PARSE_SET = 358;
	public const BLOB_PARSE_SET = 359;

	public const OUTPUT_INDEX = 401;
	public const OBJECT_CLASS_EXISTS = 402;
	public const OBJECT_METHOD_EXISTS = 403;

	public function __construct(int $code)
	{
		$args = array_slice(func_get_args(), 1);
		$previous = end($args) instanceof \Throwable ? array_pop($args) : null;
		$format = self::getDefaultMessage($code);
		array_unshift($args, $format);
		$message = format($args);

		parent::__construct($message, $code, $previous);
	}

	public static function getDefaultMessage(int $code): string
	{
		if (isset(self::$customMessages[$code]))
			return self::$customMessages[$code];

		switch ($code)
		{
			case self::SCHEMA_CHANGE:		return 'failure on change schema to "%s"';
			case self::AUTO_COMMIT_CHANGE:	return 'failure on change autocommit to "%s"';
			case self::CHARSET_CHANGE:		return 'failure on change charset to "%s"';
			case self::EMPTY_HOST:			return 'connection host undefined';
			case self::EMPTY_USERNAME:		return 'connection username undefined';
			case self::EMPTY_SCHEMA:		return 'connection schema undefined';
			case self::EMPTY_PORT:			return 'connection port undefined';
			case self::CONNECTED:			return 'connection already established';
			case self::CLOSED:				return 'connection closed';
			case self::TRANSACTION_FAILURE:	return 'failure on begin a new transaction';
			case self::COMMIT_FAILURE:		return 'failure on commit transaction';
			case self::ROLLBACK_FAILURE:	return 'failure on rollback transaction';
			case self::FETCH_MODE_CHANGE:	return 'failure on change fetch mode (fetchMode: %d)';
			case self::FETCH_SIZE_CHANGE:	return 'failure on change fetch size (fetchSize: %d)';
			case self::QUERY_TIMEOUT_CHANGE:return 'failure on change query timeout (queryTimeout: %d)';

			case self::CLASS_NAME:			return 'invalid class name informed (className: %s)';
			case self::NO_RESULTS:			return 'there are no resulsts';
			case self::EXECUTE_QUERY:		return 'failure on execute statement [%s]';
			case self::CLOSED_STATEMENT:	return 'statement closed';

			case self::PARAMETER_DATA_TYPE:	return 'unsupported data type (dataType: %s)';
			case self::PDO_CONNECTION:		return 'there are no PDO connection';
			case self::FETCH_MODE:			return 'unsupported fetch mode (fetchMode: %d)';
			case self::COLUMN_NOT_FOUND:	return 'column not found on result set (column: %s)';
			case self::OBJECT_INSTANCE:		return 'failure on instance a %s (%s)';
			case self::OBJECT_PARSE:		return 'cannot parse the result set into "%s"';
			case self::ROW_NOT_FOUND:		return 'row not found on resultset (row: %d)';
			case self::JSON_OBJECT:			return 'expected a JsonObject class name (className: %s)';
			case self::OBJECT_CLASS_NAME:	return 'result entry cannot be auto casted into a object';
			case self::PARAMETER_INDEX:		return 'invalid paramter index (parameterIndex: %s)';
			case self::WARNINGS:			return 'failure on execute show warnings';
			case self::PARAMETER_OBJECT:	return 'unsupported object parameter (parameterIndex: %s, class_name: %s)';
			case self::CLOSE_CURSOR:		return 'failure on close statement cursor';

			case self::BOOL_PARSE_GET:		return 'cannot parse "%s" into a bool (column: %s)';
			case self::BYTE_PARSE_GET:		return 'cannot parse "%s" into a byte (column: %s)';
			case self::SHORT_PARSE_GET:		return 'cannot parse "%s" into a short (column: %s)';
			case self::INT_PARSE_GET:		return 'cannot parse "%s" into a int (column: %s)';
			case self::LONG_PARSE_GET:		return 'cannot parse "%s" into a long (column: %s)';
			case self::FLOAT_PARSE_GET:		return 'cannot parse "%s" into a float (column: %s)';
			case self::DOUBLE_PARSE_GET:	return 'cannot parse "%s" into a double (column: %s)';
			case self::CHAR_PARSE_GET:		return 'cannot parse "%s" into a char (column: %s)';
			case self::TIME_PARSE_GET:		return 'cannot parse "%s" into a Time (column: %s)';
			case self::DATE_PARSE_GET:		return 'cannot parse "%s" into a DateTime (column: %s)';
			case self::DATETIME_PARSE_GET:	return 'cannot parse "%s" into a DateTime (column: %s)';
			case self::TIMESTAMP_PARSE_GET:	return 'cannot parse "%s" into a DateTime (column: %s)';
			case self::BLOB_PARSE_GET:		return 'cannot parse "%s" into a resource (column: %s)';

			case self::BOOL_PARSE_SET:		return 'cannot parse "%s" into a bool (parameterIndex: %s)';
			case self::BYTE_PARSE_SET:		return 'cannot parse "%s" into a byte (parameterIndex: %s)';
			case self::SHORT_PARSE_SET:		return 'cannot parse "%s" into a short (parameterIndex: %s)';
			case self::INT_PARSE_SET:		return 'cannot parse "%s" into a int (parameterIndex: %s)';
			case self::LONG_PARSE_SET:		return 'cannot parse "%s" into a long (parameterIndex: %s)';
			case self::FLOAT_PARSE_SET:		return 'cannot parse "%s" into a float (parameterIndex: %s)';
			case self::DOUBLE_PARSE_SET:	return 'cannot parse "%s" into a double (parameterIndex: %s)';
			case self::CHAR_PARSE_SET:		return 'cannot parse "%s" into a char (parameterIndex: %s)';
			case self::BLOB_PARSE_SET:		return 'cannot parse "%s" into a blob (parameterIndex: %s)';

			case self::OUTPUT_INDEX:		return 'output index not found (parameterIndex: %s)';
			case self::OBJECT_CLASS_EXISTS:	return 'class name not found (parameterIndex: %s, class_name: %s)';
			case self::OBJECT_METHOD_EXISTS:return 'cannot parse object of %s (parameterIndex: %s)';
		}
	}

	public static function getCustomMessages(): array
	{
		return self::$customMessages;
	}

	public static function getCustomMessage(int $code): ?string
	{
		return isset(self::$customMessages[$code]) ? self::$customMessages[$code] : null;
	}

	public static function setCustomMessage(array $customMessages): void
	{
		self::$customMessages = $customMessages;
	}

	public static function setCustomMessages(int $code, string $customMessage): void
	{
		self::$customMessages = self::$customMessages[$code] = $customMessage;
	}
}

