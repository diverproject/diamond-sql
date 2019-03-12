<?php

namespace test\diamond\sql;

use diamond\json\JsonObject;

class Department extends JsonObject
{
	private $dept_name;
	private $dept_no;

	public function getDept_name(): ?string
	{
		return $this->dept_name;
	}

	public function setDept_name(?string $dept_name): void
	{
		$this->dept_name = $dept_name;
	}

	public function getDept_no(): ?string
	{
		return $this->dept_no;
	}

	public function setDept_no(?string $dept_no): void
	{
		$this->dept_no = $dept_no;
	}

}

