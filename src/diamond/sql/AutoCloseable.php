<?php

namespace diamond\sql;

interface AutoCloseable
{
	public function close(): void;
}

