<?php
/** @version	2012-12-09-1 */
use Tester\Assert,
	Heymaster\Cli\Runner;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Cli/IRunner.php';
require __DIR__ . '/../../../src/Cli/Runner.php';

Assert::same("'-n'", Runner::escapeArg('-n'));
Assert::same("'Hello, Runner!'", Runner::escapeArg('Hello, Runner!'));

