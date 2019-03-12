<?php

namespace diamond\sql;

interface MetaData
{
	public function getDriverName(): string;
	public function getServerInfo(): string;
	public function getServerVersion(): string;
	public function getClientVersion(): string;
	public function getConnectionStatus(): string;
	public function isPersistent(): bool;
	public function getCase(): int;
	public function getOracleNulls(): int;
	public function getPreFetch(): int;
	public function getErrorMode(): int;
	public function getTimeout(): int;
}

