<?php

namespace diamond\sql;

use diamond\json\JsonObject;
use diamond\lang\IntParser;
use diamond\lang\exceptions\UnsupportedMethodException;

class Time extends JsonObject
{
	public const REGEX = '/^(?<hours>[-+]?([0-9]|[1-8][0-9]|9[0-9]|[1-7][0-9]{2}|8[0-2][0-9]|83[0-8]))\:(?<minutes>[0-5][0-9])\:(?<seconds>[0-5][0-9]$)/';

	public const MINUTE_SECONDS = 60;
	public const HOUR_MINUTES = 60;

	public const SECOND_TIME = 1;
	public const MINUTE_TIME = self::SECOND_TIME * self::MINUTE_SECONDS;
	public const HOUR_TIME = self::MINUTE_TIME * self::HOUR_MINUTES;

	public const MIN_TIME = -((self::HOUR_TIME * 839) - 1);
	public const MAX_TIME = +((self::HOUR_TIME * 839) - 1);

	private $time;

	public function __construct($time = '')
	{
		$this->time = 0;
		if (is_int($time))
			$this->setTime(intval($time));
		else if (is_string($time))
			$this->setFullTimeFormat(strval($time));
		else
			throw new UnsupportedMethodException('time need be int (seconds) or string (formatte)');
	}

	public function getSeconds(): int
	{
		return abs($this->time) % self::MINUTE_SECONDS;
	}

	public function setSeconds(int $seconds): void
	{
		$this->setFullTime($this->getHours(), $this->getMinutes(), $seconds);
	}

	public function getMinutes(): int
	{
		return (abs($this->time) % self::HOUR_TIME) / self::HOUR_MINUTES;
	}

	public function setMinutes(int $minutes): void
	{
		$this->setFullTime($this->getHours(), $minutes, $this->getSeconds());
	}

	public function getHours(): int
	{
		return $this->time / self::HOUR_TIME;
	}

	public function setHours(int $hours): void
	{
		$this->setFullTime($hours, $this->getMinutes(), $this->getSeconds());
	}

	public function setFullTime(int $hours, int $minutes, int $seconds): void
	{
		if ($hours > 0)
			$this->time = ($hours * self::HOUR_TIME) + ($minutes * self::MINUTE_TIME) + $seconds;
		else
			$this->time = ($hours * self::HOUR_TIME) - ($minutes * self::MINUTE_TIME) - $seconds;
	}

	public function setFullTimeFormat(string $formatted): bool
	{
		$matches = [];

		if (preg_match_all(self::REGEX, $formatted, $matches) !== 1)
			return false;

		$this->setFullTime($matches['hours'][0], $matches['minutes'][0], $matches['seconds'][0]);
		return true;
	}

	public function getTime(): int
	{
		return $this->time;
	}

	public function setTime(int $time): void
	{
		$this->time = IntParser::cap($time, self::MIN_TIME, self::MAX_TIME);
	}

	/**
	 * @JsonAnnotation({"name":"formatted"})
	 * @return string
	 */
	public function getFormatted(): string
	{
		return format('%s%02d:%02d:%02d', ternary($this->time < 0, '-', ''), abs($this->getHours()), $this->getMinutes(), $this->getSeconds());
	}

	public function __toString(): string
	{
		return $this->getFormatted();
	}
}

