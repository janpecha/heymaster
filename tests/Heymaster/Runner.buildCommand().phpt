<?php
/** @version	2012-12-07-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Cli/IRunner.php';
require __DIR__ . '/../../src/Cli/Runner.php';

class Runner extends \Heymaster\Cli\Runner
{
	public static function buildCommand($cmd)
	{
		return parent::buildCommand($cmd);
	}
}

Assert::same("php '-f' 'any-file.php' '--' 'first arg'", Runner::buildCommand(array(
	'php',
	'-f' => 'any-file.php',
	'--',
	'first arg',
)));

Assert::false(Runner::buildCommand(array()));

Assert::same('php', Runner::buildCommand('php'));

