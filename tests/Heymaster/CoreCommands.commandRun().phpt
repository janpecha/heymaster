<?php
/** @version	2012-12-07-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/exceptions.php';
require __DIR__ . '/../../src/Cli/IRunner.php';
require __DIR__ . '/../../src/Cli/Runner.php';
require __DIR__ . '/../../src/Commands/CommandSet.php';
require __DIR__ . '/../../src/Commands/CoreCommands.php';
require __DIR__ . '/../../src/Command.php';

class Runner
{
	public $returnCode = 1;
	
	
	public function run()
	{
		return $this->returnCode;
	}
}

class CoreCommands extends Heymaster\Commands\CoreCommands
{
	public function __construct()
	{
		$this->heymaster = new stdClass;
		$this->heymaster->runner = new Runner;
	}
	
	
	public function getRunner()
	{
		return $this->heymaster->runner;
	}
}

$core = new CoreCommands;
$command = new Heymaster\Command;
$command->params = array();
$command->name = 'TestCommand';

Assert::throws(function() use ($core, $command) {
	$core->commandRun($command, '');
}, 'Heymaster\\InvalidException');


$command->params = array(
	'cmd' => 'any-command -p',
);

Assert::throws(function() use ($core, $command) {
	$core->commandRun($command, '');
}, 'UnexpectedValueException');


$command->params['fatal'] = FALSE;
$core->commandRun($command, '');


$command->params['fatal'] = TRUE;

$core->getRunner()->returnCode = 0;
$core->commandRun($command, '');

unset($command->params['fatal']);
$core->commandRun($command, '');

$core->getRunner()->returnCode = 1;
Assert::throws(function() use ($core, $command) {
	$core->commandRun($command, '');
}, 'UnexpectedValueException');


