<?php

namespace diamond\sql;

class SqlWarning
{
	private $level;
	private $code;
	private $message;

	public function getLevel(): string
	{
		return $this->level;
	}

	public function setLevel(string $level): void
	{
		$this->level = $level;
	}

	public function getCode(): int
	{
		return $this->code;
	}

	public function setCode(int $code): void
	{
		$this->code = $code;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function setMessage(string $message): void
	{
		$this->message = $message;
	}
}

