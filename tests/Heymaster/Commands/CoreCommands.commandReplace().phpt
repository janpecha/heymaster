<?php
/** @version	2012-12-17-1 */
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/exceptions.php';
require __DIR__ . '/../../../src/Commands/CommandSet.php';
require __DIR__ . '/../../../src/Commands/CoreCommands.php';
require __DIR__ . '/../../../src/Command.php';


class CoreCommands extends Heymaster\Commands\CoreCommands
{
	public function __construct()
	{
	}
}

$core = new CoreCommands;
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
file_put_contents($file1, "Lorem ipsum\ndolor sit\namet");
mkdir($dir, 0777);
file_put_contents($file2, 'Ahoj');

$core->commandReplace($command, '');
$content = file_get_contents($file1);
unlink($file1);
unlink($file2);
rmdir($dir);
Assert::same('Ahoj', $content);

