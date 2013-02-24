<?php
/** @version	2013-02-24-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Config.php';
require __DIR__ . '/../../src/Action.php';

$config = new Heymaster\Config;
$action = new Heymaster\Action;
$action->config = new Heymaster\Config;
Assert::null($action->process($dummyScope = NULL, $config));


$action->runnable = FALSE;
$action->commands = array();
Assert::null($action->process($dummyScope = NULL, $config));


class DummyCommand
{
	public function process()
	{
		return;
	}
}

$dummyCommand = new DummyCommand;

$action->commands[] = $dummyCommand;
$action->runnable = TRUE;
Assert::true($action->process($dummyScope = NULL, $config));

