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

// Empty params
Assert::throws(function() use ($core, $command, $config) {
	$core->commandTouch($command, $config, NULL);
}, 'Heymaster\\InvalidException');


// Normal - no force
$command->params = array(
	'mask' => array(
		'.gitignore',
		'subdir/readme.txt',
	),
);

$command->callback = array($core, 'commandTouch');
$command->process($scope, $config, NULL);

Assert::true(file_exists(TEMP_DIR . '/.gitignore'));
Assert::true(file_exists(TEMP_DIR . '/subdir/readme.txt'));
purge(TEMP_DIR);


// Normal - force
$refl = $command->getReflection()->getProperty('scope');
$refl->setAccessible(TRUE);
$refl->setValue($command, NULL);

file_put_contents(TEMP_DIR . '/.gitignore', "Lorem Ipsum\ndolor\nsit amet");
mkdir(TEMP_DIR . '/subdir');
file_put_contents(TEMP_DIR . '/subdir/readme.txt', "Lorem Ipsum\ndolor\nsit amet.\n\n-- Gandalf");
$command->params['force'] = TRUE;
$command->process($scope, $config, NULL);

Assert::same('', file_get_contents(TEMP_DIR . '/.gitignore'));
Assert::same('', file_get_contents(TEMP_DIR . '/subdir/readme.txt'));


