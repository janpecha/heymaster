<?php
/** @version	2013-02-26-1 */
use Tester\Assert,
	Heymaster\Commands\CoreCommands,
	Heymaster\Files\FileManipulator;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Files/FileManipulator.php';
require __DIR__ . '/../../../src/Cli/IRunner.php';
#require __DIR__ . '/../../../src/Cli/Runner.php';
require __DIR__ . '/../../../src/Config.php';
require __DIR__ . '/../../../src/Commands/CommandSet.php';
require __DIR__ . '/../../../src/Commands/CoreCommands.php';
require __DIR__ . '/../../../src/Command.php';

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

$runner = new Runner;
$core = new CoreCommands($runner, new FileManipulator());
$command = new Heymaster\Command;
$command->params = array();
$command->name = 'TestCommand';
$command->config = new stdClass;
$command->config->output = TRUE;

$config = new Heymaster\Config;

Assert::throws(function() use ($core, $command, $config) {
	$core->commandRun($command, $config, NULL);
}, 'Heymaster\\InvalidException');


$command->params = array(
	'cmd' => 'any-command -p',
);

Assert::throws(function() use ($core, $command, $config) {
	$core->commandRun($command, $config, NULL);
}, 'UnexpectedValueException');


$command->params['fatal'] = FALSE;
$core->commandRun($command, $config, NULL);


$command->params['fatal'] = TRUE;

$runner->returnCode = 0;
$core->commandRun($command, $config, NULL);

unset($command->params['fatal']);
$core->commandRun($command, $config, NULL);

$runner->returnCode = 1;
Assert::throws(function() use ($core, $command, $config) {
	$core->commandRun($command, $config, NULL);
}, 'UnexpectedValueException');


