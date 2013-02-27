<?php

class Runner implements Heymaster\Cli\IRunner
{
	public $returnCode = 1;
	
	
	public function run($cmd, &$output)
	{
		return $this->returnCode;
	}
	
	public static function escapeArg($arg)
	{
		return escapeshellarg($arg);
	}
}

