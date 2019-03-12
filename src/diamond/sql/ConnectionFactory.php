<?php

namespace diamond\sql;

interface ConnectionFactory
{
	public function newConnectionBuilder(): ConnectionBuilder;
}

