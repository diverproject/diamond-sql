<?php

namespace diamond\sql\pdo;

use diamond\json\JsonObject;

class PDOOptions extends JsonObject
{
	public const DEFAULT_PERSISTENT = true;

	/**
	 * @JsonAnnotation({"name":"ATTR_PERSISTENT"})
	 * @var bool
	 */
	private $persistent;

	public function __construct()
	{
		$this->setPersistent(self::DEFAULT_PERSISTENT);
	}

	public function getPersistent(): ?bool
	{
		return $this->persistent;
	}

	public function setPersistent(?bool $persistent): void
	{
		$this->persistent = $persistent;
	}

}

