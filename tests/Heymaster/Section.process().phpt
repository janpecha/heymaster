<?php
/** @version	2013-02-23-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Logger/ILogger.php';
require __DIR__ . '/../../src/Config.php';
require __DIR__ . '/../../src/Scopes/Scope.php';
require __DIR__ . '/../../src/Section.php';

class DummyLogger implements Heymaster\Logger\ILogger
{
	function log($str)
	{
	}
	
	function error($str)
	{
	}
	
	function success($str)
	{
	}
	
	function warn($str)
	{
	}
	
	function info($str)
	{
	}
	
	function prefix($prefix)
	{
	}
	
	function end()
	{
	}
}

$logger = new DummyLogger;
$scope = new Heymaster\Scopes\Scope(__DIR__, $logger);

$section = new Heymaster\Section;
$section->config = new Heymaster\Config;
$section->scope = $scope;

Assert::null($section->process());

class DummyAction
{
	public $config;
	
	public function process()
	{
		return;
	}
}

$action = new DummyAction;
$action->config = new Heymaster\Config;

$section->actions[] = $action;
Assert::true($section->process());

