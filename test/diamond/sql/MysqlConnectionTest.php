<?php

namespace test\diamond\sql;

use DateTime;
use diamond\lang\FloatParser;
use diamond\lang\System;
use diamond\sql\Connection;
use diamond\sql\pdo\mysql\MysqlPDOConnection;
use diamond\sql\pdo\PDOConnectionFactory;
use diamond\sql\pdo\PDOResultSetFactory;
use diamond\sql\pdo\PDOUtil;
use diamond\sql\SqlException;
use diamond\sql\Statement;
use diamond\sql\Time;
use PDOStatement;

/**
 * @author Andrew
 */
class MysqlConnectionTest extends DiamondSqlTest
{
	public const BLOB_FILE_PATH = 'test/diamond/res/download.png';

	public function newConnection(): Connection
	{
		$connectionFactory = new PDOConnectionFactory();
		$connectionBuilder = $connectionFactory->newConnectionBuilder();
		$connection = $connectionBuilder->newMysqlConnection(self::HOST, self::USERNAME, self::PASSWORD, self::SCHEMA);

		return $connection;
	}

	public function testAutoCommit()
	{
		$connection = $this->newConnection();
		$connection->connect();

		$secondConnection = $this->newConnection();
		$secondConnection->connect();

		$this->assertTrue($connection instanceof MysqlPDOConnection);
		$this->assertTrue($secondConnection instanceof MysqlPDOConnection);

		$this->assertTrue($connection instanceof MysqlPDOConnection);
		{
			$connection->setAutoCommit(false);
			$this->assertFalse($connection->isAutoCommit());
			$connection->setAutoCommit(true);
			$this->assertTrue($connection->isAutoCommit());

			// Rollback Tests
			$connection->setAutoCommit(false);
			$connection->beginTransaction();
			{
				$this->assertEquals('Customer Service', $this->getDepartment($connection));
				$this->assertTrue($this->updateDepartment($connection, 'Rollback Department'));
				$this->assertEquals('Customer Service', $this->getDepartment($secondConnection));
				$this->assertEquals('Rollback Department', $this->getDepartment($connection));
			}
			$connection->rollback();
			$this->assertEquals('Customer Service', $this->getDepartment($secondConnection));
			$this->assertEquals('Customer Service', $this->getDepartment($connection));

			// Commit Tests
			$connection->setAutoCommit(false);
			$connection->beginTransaction();
			{
				$this->assertEquals('Customer Service', $this->getDepartment($connection));
				$this->assertTrue($this->updateDepartment($connection, 'Rollback Department'));
				$this->assertEquals('Rollback Department', $this->getDepartment($connection));
				$this->assertEquals('Customer Service', $this->getDepartment($secondConnection));
			}
			$connection->commit();
			$connection->beginTransaction();
			{
				$this->assertEquals('Rollback Department', $this->getDepartment($secondConnection));
				$this->assertEquals('Rollback Department', $this->getDepartment($connection));
				$this->assertTrue($this->updateDepartment($connection, 'Customer Service'));
			}
			$connection->commit();
			$this->assertEquals('Customer Service', $this->getDepartment($secondConnection));
			$this->assertEquals('Customer Service', $this->getDepartment($connection));

			// Autocommit Tests
			$connection->setAutoCommit(true);
			$this->assertEquals('Customer Service', $this->getDepartment($connection));
			$this->assertTrue($this->updateDepartment($connection, 'Rollback Department'));
			$this->assertEquals('Rollback Department', $this->getDepartment($secondConnection));
			$this->assertEquals('Rollback Department', $this->getDepartment($connection));
			$this->assertTrue($this->updateDepartment($connection, 'Customer Service'));
			$this->assertEquals('Customer Service', $this->getDepartment($secondConnection));
			$this->assertEquals('Customer Service', $this->getDepartment($connection));
		}

		$secondConnection->close();
		$connection->close();
	}

	public function testStatementBoth()
	{
		$connection = $this->newConnection();
		$connection->connect();
		{
			$expectedDepartments = [
				[ 'dept_name' => 'Marketing',			'dept_no' => 'd001',	'0' => 'Marketing',				'1' => 'd001', ],
				[ 'dept_name' => 'Finance',				'dept_no' => 'd002',	'0' => 'Finance',				'1' => 'd002', ],
				[ 'dept_name' => 'Human Resources',		'dept_no' => 'd003',	'0' => 'Human Resources',		'1' => 'd003', ],
				[ 'dept_name' => 'Production',			'dept_no' => 'd004',	'0' => 'Production',			'1' => 'd004', ],
				[ 'dept_name' => 'Development',			'dept_no' => 'd005',	'0' => 'Development',			'1' => 'd005', ],
				[ 'dept_name' => 'Quality Management',	'dept_no' => 'd006',	'0' => 'Quality Management',	'1' => 'd006', ],
				[ 'dept_name' => 'Sales',				'dept_no' => 'd007',	'0' => 'Sales',					'1' => 'd007', ],
				[ 'dept_name' => 'Research',			'dept_no' => 'd008',	'0' => 'Research',				'1' => 'd008', ],
				[ 'dept_name' => 'Customer Service',	'dept_no' => 'd009',	'0' => 'Customer Service',		'1' => 'd009', ],
			];

			$statement = $connection->createStatement();
			$statement->setFetchMode(Statement::FETCH_BOTH);
			$result = $statement->executeQuerySql("SELECT dept_name, dept_no FROM departments ORDER BY dept_no");

			foreach ($result as $index => $department)
				$this->assertEquals($expectedDepartments[$index], $department);

			$result->close();
		}
		$connection->close();
	}

	public function testStatementColumnIndex()
	{
		$connection = $this->newConnection();
		$connection->connect();
		{
			$expectedDepartments = [
				[ '0' => 'Marketing',			'1' => 'd001', ],
				[ '0' => 'Finance',				'1' => 'd002', ],
				[ '0' => 'Human Resources',		'1' => 'd003', ],
				[ '0' => 'Production',			'1' => 'd004', ],
				[ '0' => 'Development',			'1' => 'd005', ],
				[ '0' => 'Quality Management',	'1' => 'd006', ],
				[ '0' => 'Sales',				'1' => 'd007', ],
				[ '0' => 'Research',			'1' => 'd008', ],
				[ '0' => 'Customer Service',	'1' => 'd009', ],
			];

			$statement = $connection->createStatement();
			$statement->setFetchMode(Statement::FETCH_INDEX);
			$result = $statement->executeQuerySql("SELECT dept_name, dept_no FROM departments ORDER BY dept_no");

			foreach ($result as $index => $department)
				$this->assertEquals($expectedDepartments[$index], $department);

			$result->close();
		}
		$connection->close();
	}

	public function testStatementColumnName()
	{
		$connection = $this->newConnection();
		$connection->connect();
		{
			$expectedDepartments = [
				[ 'dept_name' => 'Marketing',			'dept_no' => 'd001', ],
				[ 'dept_name' => 'Finance',				'dept_no' => 'd002', ],
				[ 'dept_name' => 'Human Resources',		'dept_no' => 'd003', ],
				[ 'dept_name' => 'Production',			'dept_no' => 'd004', ],
				[ 'dept_name' => 'Development',			'dept_no' => 'd005', ],
				[ 'dept_name' => 'Quality Management',	'dept_no' => 'd006', ],
				[ 'dept_name' => 'Sales',				'dept_no' => 'd007', ],
				[ 'dept_name' => 'Research',			'dept_no' => 'd008', ],
				[ 'dept_name' => 'Customer Service',	'dept_no' => 'd009', ],
			];

			$statement = $connection->createStatement();
			$statement->setFetchMode(Statement::FETCH_COLUMN);
			$result = $statement->executeQuerySql("SELECT dept_name, dept_no FROM departments ORDER BY dept_no");

			foreach ($result as $index => $department)
				$this->assertEquals($expectedDepartments[$index], $department);

			$result->close();
		}
		$connection->close();
	}

	public function testPreparedStatement()
	{
		$tests = [
			[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_tinyint',
				'method' => 'setByte',
				'statements' => [
					[ 'parameter' => PDOUtil::MIN_BYTE, ],
					[ 'parameter' => PDOUtil::MAX_BYTE, ],
					[ 'parameter' => PDOUtil::MIN_BYTE - 1, 'exception' => SqlException::BYTE_PARSE_SET ],
					[ 'parameter' => PDOUtil::MAX_BYTE + 1, 'exception' => SqlException::BYTE_PARSE_SET ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_smallint',
				'method' => 'setShort',
				'statements' => [
					[ 'parameter' => PDOUtil::MIN_SHORT, ],
					[ 'parameter' => PDOUtil::MAX_SHORT, ],
					[ 'parameter' => PDOUtil::MIN_SHORT - 1, 'exception' => SqlException::SHORT_PARSE_SET ],
					[ 'parameter' => PDOUtil::MAX_SHORT + 1, 'exception' => SqlException::SHORT_PARSE_SET ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_int',
				'method' => 'setInt',
				'statements' => [
					[ 'parameter' => PDOUtil::MIN_INT, ],
					[ 'parameter' => PDOUtil::MAX_INT, ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_float',
				'method' => 'setFloat',
				'statements' => [
					[ 'parameter' => PDOUtil::MIN_FLOAT, ],
					[ 'parameter' => PDOUtil::MAX_FLOAT, ],
					[ 'parameter' => -0.000001, 'exception' => SqlException::FLOAT_PARSE_SET ],
					[ 'parameter' => 9999999, 'exception' => SqlException::FLOAT_PARSE_SET ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_float',
				'method' => 'setUnsafeFloat',
				'statements' => [
					[ 'parameter' => FloatParser::MIN_FLOAT_32, ],
					[ 'parameter' => FloatParser::MAX_FLOAT_32, ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_double',
				'method' => 'setUnsafeDouble',
				'statements' => [
					[ 'parameter' => FloatParser::MIN_FLOAT_64, ],
					[ 'parameter' => FloatParser::MAX_FLOAT_64, ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_boolean',
				'method' => 'setBool',
				'statements' => [
					[ 'parameter' => true, ],
					[ 'parameter' => false, ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_char',
				'method' => 'setString',
				'statements' => [
					[ 'parameter' => 'A', ],
					[ 'parameter' => 'a', ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_varchar',
				'method' => 'setString',
				'statements' => [
					[ 'parameter' => 'ABCDEFJG', ],
					[ 'parameter' => 'abcdefjg', ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_tinytext',
				'method' => 'setString',
				'statements' => [
					[ 'parameter' => ($allAscii = $this->newTextAllChars()), ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_text',
				'method' => 'setString',
				'statements' => [
					[ 'parameter' => $allAscii, ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_mediumtext',
				'method' => 'setString',
				'statements' => [
					[ 'parameter' => $allAscii, ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_longtext',
				'method' => 'setString',
				'statements' => [
					[ 'parameter' => $allAscii, ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_time',
				'method' => 'setTime',
				'statements' => [
					[ 'parameter' => ($time = new Time(Time::MIN_TIME)), 'result' => $time->getFormatted() ],
					[ 'parameter' => ($time = new Time(Time::MAX_TIME)), 'result' => $time->getFormatted() ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_date',
				'method' => 'setDateTime',
				'statements' => [
					[ 'parameter' => ($datetime = new DateTime), 'result' => $datetime->format(PDOUtil::DATE_FORMAT) ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_datetime',
				'method' => 'setDateTime',
				'statements' => [
					[ 'parameter' => ($datetime = new DateTime), 'result' => $datetime->format(PDOUtil::DATETIME_FORMAT) ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_timestamp',
				'method' => 'setTimestamp',
				'statements' => [
					[ 'parameter' => ($datetime = new DateTime), 'result' => $datetime->format(PDOUtil::DATETIME_FORMAT) ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_year',
				'method' => 'setTimestamp',
				'statements' => [
					[ 'parameter' => ($datetime = new DateTime), 'result' => $datetime->format('Y') ],
				],
			],[
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_blob',
				'method' => 'setBlob',
				'statements' => [
					[ 'parameter' => fopen(self::BLOB_FILE_PATH, 'rb'), 'result' => file_get_contents(self::BLOB_FILE_PATH) ],
				],
			],
		];

		if ($this->architecture === System::isArchitecture64())
		{
			$tests[] = [
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_int',
				'method' => 'setInt',
				'statements' => [
					[ 'parameter' => 2147483648, 'exception' => SqlException::INT_PARSE_SET, ],
					[ 'parameter' => -2147483649, 'exception' => SqlException::INT_PARSE_SET, ],
				],
			];
			$tests[] = [
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_bigint',
				'method' => 'setLong',
				'statements' => [
					[ 'parameter' => 9223372036854775807, ],
					[ 'parameter' => -9223372036854775808, ],
				],
			];
			$tests[] = [
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_float',
				'method' => 'setFloat',
				'statements' => [
					[ 'parameter' => 9.999999, 'exception' => SqlException::FLOAT_PARSE_SET, ],
					[ 'parameter' => 999999.9, 'exception' => SqlException::FLOAT_PARSE_SET, ],
					[ 'parameter' => -9.999999, 'exception' => SqlException::FLOAT_PARSE_SET, ],
					[ 'parameter' => -999999.9, 'exception' => SqlException::FLOAT_PARSE_SET, ],
				],
			];
			$tests[] = [
				'query' => 'UPDATE data_type SET %s = ?',
				'column' => 'var_double',
				'method' => 'setFloat',
				'statements' => [
					[ 'parameter' => PDOUtil::MIN_DOUBLE, ],
					[ 'parameter' => PDOUtil::MAX_DOUBLE, ],
				],
			];
		}

		$connection = $this->newConnection();
		$connection->connect();
		{
			$this->assertTrue($connection instanceof MysqlPDOConnection);

			foreach ($tests as $test)
			{
				$sql = sprintf($test['query'], $test['column']);
				$method = $test['method'];
				$statement = $connection->prepareStatement($sql);

				foreach ($test['statements'] as $data)
				{
					$statement->clearParameters();

					try {
						$statement->$method(1, $data['parameter']);
					} catch (SqlException $e) {
						if (!isset($data['exception']))
							throw $e;
						$this->assertEquals($data['exception'], $e->getCode());
						continue;
					}

					$statement->executeUpdate();
					$dataType = $this->getDataType($connection);

					if (isset($data['result']))
						$this->assertEquals($dataType[$test['column']], $data['result']);
					else
						$this->assertEquals($dataType[$test['column']], $data['parameter']);
				}
			}
		}
		$connection->close();
	}

	public function testCallStatement()
	{
		$connection = $this->newConnection();
		$connection->connect();
		{
			$statement = $connection->prepareCall("CALL procedure_input_update(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			{
				$statement->addByte(($byte = rand(PDOUtil::MIN_BYTE, PDOUtil::MAX_BYTE)));
				$statement->addShort(($short = rand(PDOUtil::MIN_SHORT, PDOUtil::MAX_SHORT)));
				$statement->addInt(($int = rand(PDOUtil::MIN_INT, PDOUtil::MAX_INT)));
				$statement->addLong(($long = System::isArchitecture32() ? null : rand(PDOUtil::MIN_LONG, PDOUtil::MAX_LONG)));
				$statement->addFloat(($float = $this->rand_float(-999999, 999999)));
				$statement->addDouble(($double = System::isArchitecture32() ? null : $this->rand_float(-9999999999999999, 9999999999999999)));
				$statement->addBool(($bool = rand(null, null) % 2 === 0));
				$statement->addChar((chr($char = rand(0, 127))));
				$statement->addString(($string = substr(md5(rand(null, null)), 0, 8)));
				$statement->addTime(($time = new Time(rand(Time::MIN_TIME, Time::MAX_TIME))));
				$statement->addDate(($date = new DateTime()));
				$statement->addDateTime(($datetime = new DateTime()));
				$statement->addTimestamp(($timestamp = new DateTime()));
				$statement->addBlob(fopen(self::BLOB_FILE_PATH, 'rb'));
			}
			$this->assertTrue($statement->execute());
			$statement->close();

			$statement = $connection->prepareCall("CALL procedure_input_update(:byte, :short, :int, :long, :float, :double, :bool, :char, :string, :time, :date, :datetime, :timestamp, :blob)");
			{
				$statement->setByte('byte', $byte);
				$statement->setShort('short', $short);
				$statement->setInt('int', $int);
				$statement->setLong('long', $long);
				$statement->setFloat('float', $float);
				$statement->setDouble('double', $double);
				$statement->setBool('bool', $bool);
				$statement->setChar('char', chr($char));
				$statement->setString('string', $string);
				$statement->setTime('time', $time);
				$statement->setDate('date', $date);
				$statement->setDateTime('datetime', $datetime);
				$statement->setTimestamp('timestamp', $timestamp);
				$statement->setBlob('blob', fopen(self::BLOB_FILE_PATH, 'rb'));
			}
			$this->assertTrue($statement->execute());
			$statement->close();

			$statement = $connection->prepareCall("CALL procedure_output_select(@byte, @short, @int, @long, @float, @double, @bool, @char, @string, @time, @date, @datetime, @timestamp, @blob)");
			{
				$this->assertTrue($statement->execute());
				$this->assertEquals($byte, $statement->getByteSequencial());
				$this->assertEquals($short, $statement->getShortSequencial());
				$this->assertEquals($int, $statement->getIntSequencial());
				$this->assertEquals($long, $statement->getLongSequencial());
				$this->assertEquals($float, $statement->getFloatSequencial());
				$this->assertEquals($double, $statement->getDoubleSequencial());
				$this->assertEquals($bool, $statement->getBoolSequencial());
				$this->assertEquals($char, ord($statement->getCharSequencial()));
				$this->assertEquals($string, $statement->getStringSequencial());
				$this->assertEquals($time->getTime(), $statement->getTimeSequencial()->getTime());
				$this->assertEquals($date->format(PDOUtil::DATE_FORMAT), $statement->getDateSequencial()->format(PDOUtil::DATE_FORMAT));
				$this->assertEquals($datetime->getTimestamp(), $statement->getDateTimeSequencial()->getTimestamp());
				$this->assertEquals($timestamp->getTimestamp(), $statement->getTimestampSequencial()->getTimestamp());
				$this->assertEquals(file_get_contents(self::BLOB_FILE_PATH), $statement->getBlobSequencial());
			}
			$statement->close();
		}
		$connection->close();
	}

	private function getDepartment(MysqlPDOConnection $connection): string
	{
		$dept_no = 'd009';
		$statement = $connection->getPDO()->prepare("SELECT dept_name FROM departments WHERE dept_no = ?");
		$statement->bindParam(1, $dept_no);
		$this->assertTrue($statement->execute());

		$result = $this->newResultSet($statement, $connection->getDefaultFetchMode());
		$department = $result->current();
		$result->close();

		$this->assertTrue(is_array($department));
		$this->assertTrue(isset($department['dept_name']));

		return strval($department['dept_name']);
	}

	private function updateDepartment(MysqlPDOConnection $connection, string $department): bool
	{
		return $connection->getPDO()->prepare("UPDATE departments SET dept_name = '$department' WHERE dept_no = 'd009'")->execute();
	}

	private function getDataType(MysqlPDOConnection $connection): array
	{
		$statement = $connection->getPDO()->prepare("SELECT * FROM data_type");
		$this->assertTrue($statement->execute());

		$result = $this->newResultSet($statement, $connection->getDefaultFetchMode());
		$this->assertEquals(1, $result->getRowCount());

		$dataType = $result->current();
		$result->close();

		return (array) $dataType;
	}

	private function newResultSet(PDOStatement $pdoStatement, int $fetchMode, ?string $class_name = null)
	{
		$factory = new PDOResultSetFactory();
		$builder = $factory->newPDOResultSetBuilder();
		$result = $builder->newFetch($pdoStatement, $fetchMode, $class_name);

		return $result;
	}

	private function newTextAllChars(): string
	{
		$string = '';

		for ($ascii = 0; $ascii < 255; $ascii++)
			$string .= utf8_decode(chr($ascii));

		return $string;
	}

	private function rand_float($floatPrecision = FloatParser::FLOAT_PRECISION_32): float
	{
		$precision = rand(0, $floatPrecision);
		$decimalPrecision = $floatPrecision - $precision;
		$integer = rand(0, pow(10, $precision));
		$decimal = rand(0, pow(10, $decimalPrecision));

		return floatval(sprintf('%0'.$precision.'d.%0'.($decimalPrecision).'d', $integer, $decimal));
	}
}

