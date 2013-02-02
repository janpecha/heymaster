<?php
/** @version	2013-02-02-1 */
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
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
$command->params = array();
$command->name = 'TestCommand';

Assert::throws(function() use ($core, $command) {
	$core->commandTouch($command, '');
}, 'Heymaster\\InvalidException');



