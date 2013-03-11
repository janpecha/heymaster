<?php
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Scopes/Scope.php';
require __DIR__ . '/inc/DummyLogger.php';
require __DIR__ . '/../../src/Config.php';
require __DIR__ . '/../../src/Action.php';

$logger = new DummyLogger;
$config = new Heymaster\Config;
$scope = new Heymaster\Scopes\Scope(TEMP_DIR, $logger);
$action = new Heymaster\Action;
$action->config = new Heymaster\Config;
Assert::null($action->process($scope, $config));


$action->runnable = FALSE;
$action->commands = array();
Assert::null($action->process($scope, $config));


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
Assert::true($action->process($scope, $config));

