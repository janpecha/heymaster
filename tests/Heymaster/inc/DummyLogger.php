<?php

require __DIR__ . '/../../../src/Logger/ILogger.php';

class DummyLogger implements Heymaster\Logger\ILogger
{
	function log($str)
	{
	}

	function error($str)
	{
	}

	function success($str)
	{
	}

	function warn($str)
	{
	}

	function info($str)
	{
	}

	function prefix($prefix)
	{
	}

	function end()
	{
	}
}

