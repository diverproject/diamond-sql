<?php

namespace test\diamond\sql;

use diamond\sql\Time;

/**
 * @author Andrew
 */
class TimeTest extends DiamondSqlTest
{
	public function newTimeFormat(int $time): string
	{
		return (new Time($time))->getFormatted();
	}

	public function testFormmat()
	{
		$this->assertEquals('-838:59:59', $this->newTimeFormat(-(Time::MAX_TIME + 1)));
		$this->assertEquals('-838:59:59', $this->newTimeFormat(-Time::MAX_TIME));
		$this->assertEquals('-838:59:58', $this->newTimeFormat(-(Time::MAX_TIME - 1)));
		$this->assertEquals('-01:00:01', $this->newTimeFormat(-3601));
		$this->assertEquals('-01:00:00', $this->newTimeFormat(-3600));
		$this->assertEquals('-00:59:59', $this->newTimeFormat(-3599));
		$this->assertEquals('-00:01:01', $this->newTimeFormat(-61));
		$this->assertEquals('-00:01:00', $this->newTimeFormat(-60));
		$this->assertEquals('-00:00:59', $this->newTimeFormat(-59));
		$this->assertEquals('-00:00:01', $this->newTimeFormat(-1));
		$this->assertEquals('00:00:00', $this->newTimeFormat(0));
		$this->assertEquals('00:00:01', $this->newTimeFormat(1));
		$this->assertEquals('00:00:59', $this->newTimeFormat(59));
		$this->assertEquals('00:01:00', $this->newTimeFormat(60));
		$this->assertEquals('00:01:01', $this->newTimeFormat(61));
		$this->assertEquals('00:59:59', $this->newTimeFormat(3599));
		$this->assertEquals('01:00:00', $this->newTimeFormat(3600));
		$this->assertEquals('01:00:01', $this->newTimeFormat(3601));
		$this->assertEquals('838:59:58', $this->newTimeFormat(Time::MAX_TIME - 1));
		$this->assertEquals('838:59:59', $this->newTimeFormat(Time::MAX_TIME));
		$this->assertEquals('838:59:59', $this->newTimeFormat(Time::MAX_TIME + 1));
	}
}

