<?php
/** @version	2013-02-26-1 */
use Tester\Assert,
	Heymaster\Commands\CoreCommands;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Config.php';
require __DIR__ . '/../../../src/Cli/IRunner.php';
require __DIR__ . '/../../../src/Commands/CommandSet.php';
require __DIR__ . '/../../../src/Commands/CoreCommands.php';
require __DIR__ . '/../../../src/Command.php';
require __DIR__ . '/../../../src/Files/FileManipulator.php';

require __DIR__ . '/Runner.inc.php';

$runner = new Runner;
$manipulator = new Heymaster\Files\FileManipulator;
$config = new Heymaster\Config;
$core = new CoreCommands($runner, $manipulator);
$command = new Heymaster\Command;
$command->params['files'] = array(
	'testovaci.html' => 'slozka/zdroj.html',
);
$command->name = 'TestCommand';
$command->config = new stdClass;
$command->config->root = TEMP_DIR;

$file1 = TEMP_DIR . '/testovaci.html';
$file2 = TEMP_DIR . '/slozka/zdroj.html';
$dir = TEMP_DIR . '/slozka';
$config->root = TEMP_DIR;

// Normalni situace
file_put_contents($file1, "Lorem ipsum\ndolor sit\namet");
mkdir($dir, 0777);
file_put_contents($file2, 'Ahoj');
$core->commandReplace($command, $config, NULL);
$content = file_get_contents($file1);
unlink($file1);
unlink($file2);
rmdir($dir);
Assert::same('Ahoj', $content);


// Neexistujici soubor testovaci.html
# REMOVED: file_put_contents($file1, "Lorem ipsum\ndolor sit\namet");
mkdir($dir, 0777);
file_put_contents($file2, 'Ahoj');
$core->commandReplace($command, $config, NULL);
$content = file_get_contents($file1);
unlink($file1);
unlink($file2);
rmdir($dir);
Assert::same('Ahoj', $content);


// Absolutni cesta
$command->params['files'] = array(
	'testovaci.html' => $file2,
);

file_put_contents($file1, "Lorem ipsum\ndolor sit\namet");
mkdir($dir, 0777);
file_put_contents($file2, 'Ahoj');
$core->commandReplace($command, $config, NULL);
$content = file_get_contents($file1);
unlink($file1);
unlink($file2);
rmdir($dir);
Assert::same('Ahoj', $content);

