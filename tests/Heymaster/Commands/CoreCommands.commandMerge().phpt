<?php
/** @version	2013-02-26-1 */
use Tester\Assert,
	Heymaster\Config,
	Heymaster\Command,
	Heymaster\Commands\CoreCommands,
	Heymaster\Scopes\Scope;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/CoreCommands.inc.php';
require __DIR__ . '/../file-helpers.php';

$runner = new Runner;
$logger = new DummyLogger;
$manipulator = new Heymaster\Files\FileManipulator;
$config = new Config;
$config->root = TEMP_DIR;
$core = new CoreCommands($runner, $manipulator);
$scope = new Scope(TEMP_DIR, $logger);

$command = new Command;
$command->params = array();
$command->name = 'TestCommand';
$command->config = new Config;

// Normal
$command->params = array(
	'mask' => '*',
	'file' => 'my-super-file.txt',
);
mkdir(TEMP_DIR . '/my-directory');
file_put_contents(TEMP_DIR . '/z101fa001.txt', 'Lorem');
file_put_contents(TEMP_DIR . '/z112fb002.txt', 'Ipsum');
file_put_contents(TEMP_DIR . '/z113fc003.txt', 'Dolor');
file_put_contents(TEMP_DIR . '/z114fd004.txt', 'Sit');
file_put_contents(TEMP_DIR . '/my-directory/f001.txt.et', 'Amet');

$command->callback = array($core, 'commandMerge');
$command->process($scope, $config, NULL);

Assert::same("Lorem\nIpsum\nDolor\nSit\nAmet\n", file_get_contents(TEMP_DIR . '/my-super-file.txt'));


